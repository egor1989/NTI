//
//  MapViewController.m
//  NTI
//
//  Created by Mike on 10.04.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "MapViewController.h"

@implementation MapViewController
@synthesize mapView = _mapView;
@synthesize routeLine = _routeLine;
//@synthesize routeLineView = routeLineView;

- (void)viewDidLoad {
    [super viewDidLoad];
    
    [_mapView setDelegate:self];
    serverCommunication = [[ServerCommunication alloc]init ];
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(mapWaitingState:)
     name: @"routePointsRequestSend"
     object: nil];
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(parseAnswer:)
     name: @"routePointsReceived"
     object: nil];
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(mapStopWaitingState)
     name: @"routePointsReceivedWithError"
     object: nil];
    
    isFirstRect = YES;
    
    //загрузка последнего маршрута
    if ([ServerCommunication checkInternetConnection]) {
        [serverCommunication getRouteFromServer:0];
    }
}

//переход карты в состояние ождиание и запрос к серверу на получение маршрута. Метод вызывается из вьюшки выбора даты
-(void) mapWaitingState: (NSNotification*) TheNotice{
    if ([ServerCommunication checkInternetConnection]){
        waintingIndicator.hidden = NO;
        [waintingIndicator startAnimating];
        grayView.hidden = NO;
        NSLog(@"getRoute");
        [serverCommunication getRouteFromServer:[[TheNotice object] timeIntervalSince1970]];
    }
}

//выход из состояния ожидания
-(void) mapStopWaitingState{
    [waintingIndicator stopAnimating];
    grayView.hidden = YES;
}

// метод верхнего уровня для отрисовки маршрута на карте. Вызывается из ServerCommunication, после получения ответа от сервера
-(void) parseAnswer: (NSNotification*) TheNotice{
    SBJsonParser *jsonParser = [SBJsonParser new];
    NSArray *answerArray = [[NSArray alloc] init];
    NSArray *routeArray = [[NSArray alloc] init];
    answerArray = [jsonParser objectWithString:[TheNotice object] error:NULL];
    routeArray = [answerArray valueForKey:@"result"];
    
    [waintingIndicator stopAnimating];
    grayView.hidden = YES;
    
    [self.mapView removeOverlays: self.mapView.overlays];
    [self parseRoute:routeArray];
    [self zoomInOnRoute];
}

// Парсит маршрут и вызывает методы отрисовки
-(void) parseRoute: (NSArray*) routeArray
{
    NSArray *point = [[NSArray alloc] init];
    NSMutableArray *routeLineArray = [[NSMutableArray alloc] init];
    NSMutableArray *allRoutesPointsArray[4][4];
    for (int i=0; i<=3; i++) {
        for (int j=0; j<=3; j++) {
            allRoutesPointsArray[i][j] = [[NSMutableArray alloc] init];
        }
    }
    if ([routeArray isEqual: @"null"]){
        NSLog(@"noHoles");
    }
    else{
        //если есть данные, начинаем заполнение массивов различными точками
        for (point in routeArray){
            NSArray *latLngArray = [[NSArray alloc] initWithObjects:[point valueForKey:@"lat"],[point valueForKey:@"lng"],nil];
            int type = [[point valueForKey:@"type"]intValue];
            int weight = [[point valueForKey:@"weight"]intValue];
//            switch (type) {
//                case 42:
//                    [routeLineArray addObject:[self createRouteLine:allRoutesPointsArray[0][0]]];
//                    break;
//                case 0:
//                    allRoutesPointsArray[0][0]
//                    
//                default:
//                    break;
//            }
            if (type == 42) {
                [routeLineArray addObject:[self createRouteLine:allRoutesPointsArray[0][0]]];
                [allRoutesPointsArray[0][0] removeAllObjects];
            }
            else{
                [allRoutesPointsArray[type][weight] addObject:latLngArray];
            }
        }
        [routeLineArray addObject:[self createRouteLine:allRoutesPointsArray[0][0]]];
        
        //отрисовка специальных точек
        for (int i=1; i<=3; i++) {
            for (int j=1; j<=3; j++) {
                [self specialPointsDraw:allRoutesPointsArray[i][j] :j];
            }
        }
        //отрисовка маршрута (чёрная линия)
        for (_routeLine in routeLineArray){
            [_mapView addOverlay:_routeLine];
        }
    }
}


//Создаёт и возвращает линию маршрута. (Просто маршрут - без специальных точек)
-(MKPolyline*) createRouteLine:(NSArray*) normalPointsArray1{
	MKMapPoint* pointArr = malloc(sizeof(CLLocationCoordinate2D) * normalPointsArray1.count);
    
	for(int idx = 0; idx < normalPointsArray1.count; idx++)
	{
		NSArray* currentPoint = [normalPointsArray1 objectAtIndex:idx];
        
		CLLocationDegrees latitude  = [[currentPoint objectAtIndex:0] doubleValue];
		CLLocationDegrees longitude = [[currentPoint objectAtIndex:1] doubleValue];
		CLLocationCoordinate2D coordinate = CLLocationCoordinate2DMake(latitude, longitude);
		MKMapPoint point = MKMapPointForCoordinate(coordinate);
        
		if (isFirstRect) {
			northEastPoint = point;
			southWestPoint = point;
            isFirstRect = NO;
		}
		else 
		{
			if (point.x > northEastPoint.x) 
				northEastPoint.x = point.x;
			if(point.y > northEastPoint.y)
				northEastPoint.y = point.y;
			if (point.x < southWestPoint.x) 
				southWestPoint.x = point.x;
			if (point.y < southWestPoint.y) 
				southWestPoint.y = point.y;
		}
        
		pointArr[idx] = point;
        
	}
    
	self.routeLine = [MKPolyline polylineWithPoints:pointArr count:normalPointsArray1.count];
	_routeRect = MKMapRectMake(southWestPoint.x, southWestPoint.y, northEastPoint.x - southWestPoint.x, northEastPoint.y - southWestPoint.y);
    
	free(pointArr);
    
    return self.routeLine;
    
//	if (nil != self.routeLine) {
//		[self.mapView addOverlay:self.routeLine];
//	}
//    self.routeLine = nil;

}

// Добавляет на карту слой - точку. В зависимости от типа точки присваивает ей определёный заголовок.
-(void) specialPointsDraw:(NSArray*) specialPointsArray: (int) pointType{
    NSArray *point = [[NSArray alloc] init ];
	for(point in specialPointsArray)
	{
		CLLocationDegrees latitude  = [[point objectAtIndex:0] doubleValue];
		CLLocationDegrees longitude = [[point objectAtIndex:1] doubleValue];
		CLLocationCoordinate2D coordinate = CLLocationCoordinate2DMake(latitude, longitude);
        
        circleSpecial = [MKCircle circleWithCenterCoordinate:coordinate radius:6];
        [circleSpecial setTitle:[NSString stringWithFormat:@"%d",pointType]];
        [self.mapView addOverlay:circleSpecial];
	}
    
}

//масштабирование на маршруте
-(void) zoomInOnRoute
{
	[self.mapView setVisibleMapRect:_routeRect];
}

- (void)dealloc 
{
	self.mapView = nil;
	self.routeLine = nil;
}


#pragma mark MKMapViewDelegate
- (MKOverlayView *)mapView:(MKMapView *)mapView viewForOverlay:(id <MKOverlay>)overlay
{
	if(overlay == self.routeLine)
	{
        MKOverlayView* overlayView = nil;
        MKPolylineView *routeLineView = [[MKPolylineView alloc] initWithPolyline:self.routeLine];
        routeLineView.fillColor = [UIColor purpleColor];
        routeLineView.strokeColor = [UIColor blueColor];
        routeLineView.lineWidth = 8;
        routeLineView.alpha = 0.6;
//        routeLineView.lineDashPhase = 15;
        
		overlayView = routeLineView;
        return overlayView;
	}
    
    MKCircle *circle = overlay;
    MKCircleView *circleView = [[MKCircleView alloc] initWithCircle:overlay];
    if([circle.title isEqualToString:@"1"]){
        circleView.strokeColor = circleView.fillColor = [UIColor greenColor];
    }
    else if([circle.title isEqualToString:@"2"]){
        circleView.strokeColor = circleView.fillColor = [UIColor yellowColor];
    }
    else if([circle.title isEqualToString:@"3"]){
        circleView.strokeColor = circleView.fillColor = [UIColor redColor];
    }
    return circleView;
}
@end
