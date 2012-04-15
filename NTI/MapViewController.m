//
//  MapViewController.m
//  NTI
//
//  Created by Mike on 10.04.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "MapViewController.h"
#define UIColorFromRGB(rgbValue) [UIColor colorWithRed:((float)((rgbValue & 0xFF0000) >> 16))/255.0 green:((float)((rgbValue & 0xFF00) >> 8))/255.0 blue:((float)(rgbValue & 0xFF))/255.0 alpha:0.8]

@implementation MapViewController
@synthesize mapView = _mapView;
@synthesize routeLine = _routeLine;
@synthesize routeLineView = routeLineView;

//routePointsReceived

// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
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
     selector: @selector(mapDrawRoute:)
     name: @"routePointsReceived"
     object: nil];
    
	// create the overlay
//	[self loadRoute];
	
	// add the overlay to the map
	if (nil != self.routeLine) {
		[self.mapView addOverlay:self.routeLine];
	}
	
	// zoom in on the route. 
	[self zoomInOnRoute];
	
}

-(void) mapWaitingState: (NSNotification*) TheNotice{
    waintingIndicator.hidden = NO;
    [waintingIndicator startAnimating];
    grayView.hidden = NO;
    NSLog(@"getRoute");
    [serverCommunication getRouteFromServer:[[TheNotice object] timeIntervalSince1970]];
}

-(void) mapDrawRoute: (NSNotification*) TheNotice{
    [waintingIndicator stopAnimating];
    grayView.hidden = YES;
    
	[self loadRoute:[TheNotice object]];
    //возможно понадобится чистить слой 
	[self zoomInOnRoute];
}

-(void) loadRoute: (NSString*) routeString
{
    SBJsonParser *jsonParser = [SBJsonParser new];
    NSArray *routeArray = [[NSArray alloc] init ];
    routeArray = [jsonParser objectWithString:routeString error:NULL];
    NSArray *pointsArray = [[NSArray alloc] init ]; 
    pointsArray = [routeArray valueForKey:@"result"]; 
//    NSArray *error = [answer valueForKey:@"error"];
//    NSString *info = [error valueForKey:@"info"];
//    NSInteger code = [[error valueForKey:@"code"] intValue];
//    NSLog(@"result=%@ info=%@ code=%d", result, info, code);
    NSArray *point = [[NSArray alloc] init];
    NSMutableArray *normalPointsArray = [[NSMutableArray alloc] init];
    NSMutableArray *specialPointsArray1 = [[NSMutableArray alloc] init];
    NSMutableArray *specialPointsArray2 = [[NSMutableArray alloc] init];
    NSMutableArray *specialPointsArray3 = [[NSMutableArray alloc] init];
    NSMutableArray *specialPointsArray4 = [[NSMutableArray alloc] init];
    NSMutableArray *specialPointsArray5 = [[NSMutableArray alloc] init];
    NSMutableArray *specialPointsArray6 = [[NSMutableArray alloc] init];
    if ([pointsArray isEqual: @"null"]){
        NSLog(@"noHoles");
    }
    else
        for (point in pointsArray){
            if ([[point valueForKey:@"type"] doubleValue] == 0){                
                NSArray *latLngArray = [[NSArray alloc] initWithObjects:[point valueForKey:@"lat"],[point valueForKey:@"lng"],nil ];
                [normalPointsArray addObject:latLngArray];
                NSLog(@"sdfsdf");
            }
            else if ([[point valueForKey:@"type"] doubleValue] == -3){
                NSArray *latLngArray = [[NSArray alloc] initWithObjects:[point valueForKey:@"lat"],[point valueForKey:@"lng"],nil ];
                [specialPointsArray1 addObject:latLngArray];
            }
            else if ([[point valueForKey:@"type"] doubleValue] == -2){
                NSArray *latLngArray = [[NSArray alloc] initWithObjects:[point valueForKey:@"lat"],[point valueForKey:@"lng"],nil ];
                [specialPointsArray2 addObject:latLngArray];
            }
            else if ([[point valueForKey:@"type"] doubleValue] == -1){
                NSArray *latLngArray = [[NSArray alloc] initWithObjects:[point valueForKey:@"lat"],[point valueForKey:@"lng"],nil ];
                [specialPointsArray3 addObject:latLngArray];
            }
            else if ([[point valueForKey:@"type"] doubleValue] == 1){
                NSArray *latLngArray = [[NSArray alloc] initWithObjects:[point valueForKey:@"lat"],[point valueForKey:@"lng"],nil ];
                [specialPointsArray4 addObject:latLngArray];
            }
            else if ([[point valueForKey:@"type"] doubleValue] == 2){
                NSArray *latLngArray = [[NSArray alloc] initWithObjects:[point valueForKey:@"lat"],[point valueForKey:@"lng"],nil ];
                [specialPointsArray5 addObject:latLngArray];
            } 
            else if ([[point valueForKey:@"type"] doubleValue] == 3){
                NSArray *latLngArray = [[NSArray alloc] initWithObjects:[point valueForKey:@"lat"],[point valueForKey:@"lng"],nil ];
                [specialPointsArray6 addObject:latLngArray];
            }

        }
    [self normalPointsDraw:normalPointsArray];
    [self specialPointsDraw:specialPointsArray1:1];
    [self specialPointsDraw:specialPointsArray2:2];
    [self specialPointsDraw:specialPointsArray3:3];
    [self specialPointsDraw:specialPointsArray4:4];
    [self specialPointsDraw:specialPointsArray5:5];
    [self specialPointsDraw:specialPointsArray6:6];
		
}

-(void) normalPointsDraw:(NSArray*) normalPointsArray{
    MKMapPoint northEastPoint; 
	MKMapPoint southWestPoint; 
    
	MKMapPoint* pointArr = malloc(sizeof(CLLocationCoordinate2D) * normalPointsArray.count);
    
	for(int idx = 0; idx < normalPointsArray.count; idx++)
	{
		NSArray* currentPoint = [normalPointsArray objectAtIndex:idx];
        
		CLLocationDegrees latitude  = [[currentPoint objectAtIndex:0] doubleValue];
		CLLocationDegrees longitude = [[currentPoint objectAtIndex:1] doubleValue];
		CLLocationCoordinate2D coordinate = CLLocationCoordinate2DMake(latitude, longitude);
		MKMapPoint point = MKMapPointForCoordinate(coordinate);
        
		if (idx == 0) {
			northEastPoint = point;
			southWestPoint = point;
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
    
	self.routeLine = [MKPolyline polylineWithPoints:pointArr count:normalPointsArray.count];
	_routeRect = MKMapRectMake(southWestPoint.x, southWestPoint.y, northEastPoint.x - southWestPoint.x, northEastPoint.y - southWestPoint.y);
    
	free(pointArr);
    
	if (nil != self.routeLine) {
		[self.mapView addOverlay:self.routeLine];
	}

}

-(void) specialPointsDraw:(NSArray*) specialPointsArray: (int) pointType{
	for(int idx = 0; idx < specialPointsArray.count; idx++)
	{
		NSArray* currentPoint = [specialPointsArray objectAtIndex:idx];
        
		CLLocationDegrees latitude  = [[currentPoint objectAtIndex:0] doubleValue];
		CLLocationDegrees longitude = [[currentPoint objectAtIndex:1] doubleValue];
		CLLocationCoordinate2D coordinate = CLLocationCoordinate2DMake(latitude, longitude);
        
        circleSpecial = [MKCircle circleWithCenterCoordinate:coordinate radius:6];
        switch (pointType) {
            case 1:
                [circleSpecial setTitle:@"-3"];
                [self.mapView addOverlay:circleSpecial];
                break;
            case 2:
                [circleSpecial setTitle:@"-2"];
                [self.mapView addOverlay:circleSpecial];
                break;
            case 3:
                [circleSpecial setTitle:@"-1"];
                [self.mapView addOverlay:circleSpecial];
                break;
            case 4:
                [circleSpecial setTitle:@"1"];
                [self.mapView addOverlay:circleSpecial];
                break;
            case 5:
                [circleSpecial setTitle:@"2"];
                [self.mapView addOverlay:circleSpecial];
                break;
            case 6:
                [circleSpecial setTitle:@"3"];
                [self.mapView addOverlay:circleSpecial];
                break;
            default:
                break;
        }
	}
    
}

-(void) zoomInOnRoute
{
	[self.mapView setVisibleMapRect:_routeRect];
}

- (void)didReceiveMemoryWarning {
	// Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
	
}


- (void)dealloc 
{
	self.mapView = nil;
	self.routeLine = nil;
	self.routeLineView = nil;
}


#pragma mark MKMapViewDelegate
- (MKOverlayView *)mapView:(MKMapView *)mapView viewForOverlay:(id <MKOverlay>)overlay
{
	
	if(overlay == self.routeLine)
	{
        
        MKOverlayView* overlayView = nil;
		//if we have not yet created an overlay view for this overlay, create it now. 
		if(nil == self.routeLineView)
		{
			routeLineView = [[MKPolylineView alloc] initWithPolyline:self.routeLine];
			routeLineView.fillColor = [UIColor blackColor];
			self.routeLineView.strokeColor = [UIColor blackColor];
			self.routeLineView.lineWidth = 3;
		}
		
		overlayView = self.routeLineView;
		
        return overlayView;
	}
    
    MKCircle *circle = overlay;
    MKCircleView *circleView = [[MKCircleView alloc] initWithCircle:overlay];
    if([circle.title isEqualToString:@"-3"]){
        
        circleView.strokeColor = circleView.fillColor = [UIColor colorWithRed:1.0 green:0.0 blue:0.0 alpha:1.0];
//        circleView.strokeColor = circleView.fillColor = UIColorFromRGB(0xff0000);
    }
    else if([circle.title isEqualToString:@"-2"]){
        circleView.fillColor = [UIColor colorWithRed:1.0 green:0.0 blue:0.0 alpha:0.7];
        
//        circleView.strokeColor = circleView.fillColor = UIColorFromRGB(0xffa500);
    }
    else if([circle.title isEqualToString:@"-1"]){
        circleView.strokeColor = [UIColor colorWithRed:1.0 green:0.0 blue:0.0 alpha:0.4];
//        circleView.strokeColor = circleView.fillColor = UIColorFromRGB(0xffff00);
    }
    else if([circle.title isEqualToString:@"1"]){
        circleView.fillColor = circleView.strokeColor = [UIColor colorWithRed:0.0 green:1.0 blue:0.0 alpha:0.4];
//        circleView.strokeColor = circleView.fillColor = UIColorFromRGB(0xd8ff00);
    }
    else if([circle.title isEqualToString:@"2"]){
        circleView.fillColor = [UIColor colorWithRed:0.0 green:1.0 blue:0.0 alpha:0.7];
//        circleView.strokeColor = circleView.fillColor = UIColorFromRGB(0xafff00);
    }
    else if([circle.title isEqualToString:@"3"]){
        circleView.strokeColor = [UIColor colorWithRed:0.0 green:1.0 blue:0.0 alpha:1.0];
//        circleView.strokeColor = circleView.fillColor = UIColorFromRGB(0x3bff00);
    }
    
    return circleView;
}
@end
