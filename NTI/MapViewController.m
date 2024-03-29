//
//  MapViewController.m
//  NTI
//
//  Created by Mike on 10.04.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "MapViewController.h"
#import "DatabaseActions.h"

@implementation MapViewController
@synthesize mapView = _mapView;
//@synthesize routeLineView = routeLineView;

- (void)viewDidLoad {
    [super viewDidLoad];
    tempCount = 0;
    NSLog(@"map view controller loaded");
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
    if ([ServerCommunication checkInternetConnection: YES]) {
        waintingIndicator.hidden = NO;
        [waintingIndicator startAnimating];
        grayView.hidden = NO;
        NSLog(@"getRoute");
        [serverCommunication getRouteFromServer:0];
    }
    viewLoaded = NO;
}

-(void)viewDidAppear:(BOOL)animated 
{
	[super viewWillAppear:animated];
    if (viewLoaded) {
        if ([ServerCommunication checkInternetConnection: YES]) {
            if ([DatabaseActions needLastRoute]){
                waintingIndicator.hidden = NO;
                [waintingIndicator startAnimating];
                grayView.hidden = NO;
                [serverCommunication getRouteFromServer:0];
                [DatabaseActions setNeedLastRoute:NO];
            }
        }
    }
    viewLoaded = YES;
}

//переход карты в состояние ождиание и запрос к серверу на получение маршрута. Метод вызывается из вьюшки выбора даты
-(void) mapWaitingState: (NSNotification*) TheNotice{
    if ([ServerCommunication checkInternetConnection: YES]){
        waintingIndicator.hidden = NO;
        [waintingIndicator startAnimating];
        grayView.hidden = NO;
        [serverCommunication getRouteFromServer:[[TheNotice object] doubleValue]];
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
//    NSLog(@"RouteArray points NUMBER = %d",[routeArray count]);
    
    [waintingIndicator stopAnimating];
    grayView.hidden = YES;
    
    [self.mapView removeOverlays: self.mapView.overlays];
    [self parseRoute:routeArray];
    [self zoomInOnRoute];
//    NSLog(@"tempCount = %d", tempCount);
}

// Парсит маршрут и вызывает методы отрисовки
-(void) parseRoute: (NSArray*) routeArray
{
    NSLog(@"MV parse route");
    NSArray *point = [[NSArray alloc] init];
    NSMutableArray *routeLineArray = [[NSMutableArray alloc] init];
    NSMutableArray *allRoutesPointsArray[5][4];
    for (int i=0; i<=4; i++) {
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
            if (type == 42) {
                //рисуем маршрут
                if ([allRoutesPointsArray[0][0] count] != 0){
                    [routeLineArray addObject:[self createRouteLine:allRoutesPointsArray[0][0]]];
                    [allRoutesPointsArray[0][0] removeAllObjects];
                }
            }
            else{
                [allRoutesPointsArray[0][0] addObject:latLngArray];
                if (type!=0) {
                    [allRoutesPointsArray[type][weight] addObject:latLngArray];
                }
            }
        }
        [routeLineArray addObject:[self createRouteLine:allRoutesPointsArray[0][0]]];
        
        //отрисовка маршрута (фиолетовая линия)
        @try {
            for (MKPolyline *route in routeLineArray){
                route.title = @"route";
                [_mapView addOverlay:route];
            }
            //отрисовка специальных точек
            for (int i=1; i<=4; i++) {
                for (int j=0; j<=3; j++) {
                    if (allRoutesPointsArray[i][j] != nil) {
                        [self specialPointsDraw:allRoutesPointsArray[i][j] withType:i andStrong:j];
                    }
                }
            }
        }
        @catch (NSException *exception) {
            NSLog(@"NSInvalidArgumentException in mapView");
        }
    }
}

//Создаёт и возвращает линию маршрута. (Просто маршрут - без специальных точек)
-(MKPolyline*) createRouteLine:(NSArray*) normalPointsArray1{
	MKMapPoint* pointArr = malloc(sizeof(CLLocationCoordinate2D) * normalPointsArray1.count);
    
    
    tempCount = tempCount + normalPointsArray1.count;
    
    
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
    
	MKPolyline *route = [MKPolyline polylineWithPoints:pointArr count:normalPointsArray1.count];
	_routeRect = MKMapRectMake(southWestPoint.x, southWestPoint.y, northEastPoint.x - southWestPoint.x, northEastPoint.y - southWestPoint.y);
    
	free(pointArr);
    
    return route;
}

// Добавляет на карту слой - точку. В зависимости от типа точки присваивает ей определёный заголовок.
-(void) specialPointsDraw:(NSArray*) specialPointsArray withType: (int) pointType andStrong: (int) pointStrong{
//    NSLog(@"MV parse answer");
    NSArray *point = [[NSArray alloc] init ];
    tempCount = tempCount + [specialPointsArray count];
	for(point in specialPointsArray)
	{
		CLLocationDegrees latitude  = [[point objectAtIndex:0] doubleValue];
		CLLocationDegrees longitude = [[point objectAtIndex:1] doubleValue];
		CLLocationCoordinate2D coordinate = CLLocationCoordinate2DMake(latitude, longitude);
        
        circleSpecial = [MKCircle circleWithCenterCoordinate:coordinate radius:6];
        [circleSpecial setTitle:[NSString stringWithFormat:@"type=%d.strong=%d",pointType, pointStrong]];
        [self.mapView addOverlay:circleSpecial];
	}
    
}

//масштабирование на маршруте
-(void) zoomInOnRoute
{
//    NSLog(@"MV zoom in route");
	[self.mapView setVisibleMapRect:_routeRect];
}


#pragma mark MKMapViewDelegate
- (MKOverlayView *)mapView:(MKMapView *)mapView viewForOverlay:(id <MKOverlay>)overlay
{
    if([overlay.title isEqualToString:@"route"])
	{
        MKOverlayView* overlayView = nil;
        MKPolylineView *routeLineView = [[MKPolylineView alloc] initWithOverlay:overlay];
        routeLineView.fillColor = [UIColor blueColor];
        routeLineView.strokeColor = [UIColor blueColor];
        routeLineView.lineWidth = 15;
        routeLineView.alpha = 0.5;
//        routeLineView.lineDashPhase = 15;
        
		overlayView = routeLineView;
        return overlayView;
	}
    
    MKCircle *circle = overlay;
    MKCircleView *circleView = [[MKCircleView alloc] initWithCircle:overlay];
    if([circle.title isEqualToString:@"type=1.strong=1"]){
        circleView.strokeColor = circleView.fillColor = [UIColor colorWithRed:0.0 green:1.0 blue:0.0 alpha:0.3];
    }
    else if([circle.title isEqualToString:@"type=1.strong=2"]){
        circleView.strokeColor = circleView.fillColor = [UIColor colorWithRed:0.0 green:1.0 blue:0.0 alpha:0.6];
    }
    else if([circle.title isEqualToString:@"type=1.strong=3"]){
        circleView.strokeColor = circleView.fillColor = [UIColor colorWithRed:0.0 green:1.0 blue:0.0 alpha:1.0];
    }
    
    else if([circle.title isEqualToString:@"type=2.strong=1"]){
        circleView.strokeColor = circleView.fillColor = [UIColor colorWithRed:1.0 green:0.0 blue:0.0 alpha:0.3];
    }
    else if([circle.title isEqualToString:@"type=2.strong=2"]){
        circleView.strokeColor = circleView.fillColor = [UIColor colorWithRed:1.0 green:0.0 blue:0.0 alpha:0.6];
    }
    else if([circle.title isEqualToString:@"type=2.strong=3"]){
        circleView.strokeColor = circleView.fillColor = [UIColor colorWithRed:1.0 green:0.0 blue:0.0 alpha:1.0];
    }
    
    else if([circle.title isEqualToString:@"type=3.strong=1"]){
        circleView.strokeColor = circleView.fillColor = [UIColor colorWithRed:1.0 green:1.0 blue:0.0 alpha:0.3];
    }
    else if([circle.title isEqualToString:@"type=3.strong=2"]){
        circleView.strokeColor = circleView.fillColor = [UIColor colorWithRed:1.0 green:1.0 blue:0.0 alpha:0.6];
    }
    else if([circle.title isEqualToString:@"type=3.strong=3"]){
        circleView.strokeColor = circleView.fillColor = [UIColor colorWithRed:1.0 green:1.0 blue:0.0 alpha:1.0];
    }
    
    //превышения
    if([circle.title isEqualToString:@"type=4.strong=0"]){
//        circleView.strokeColor = circleView.fillColor = [UIColor colorWithRed:0.5 green:0.42 blue:0.16 alpha:1.0];
        circleView.strokeColor = circleView.fillColor = [UIColor purpleColor];
    }
    return circleView;
}
@end
