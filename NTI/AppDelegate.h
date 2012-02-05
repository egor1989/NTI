//
//  AppDelegate.h
//  NTI
//
//  Created by Елена on 05.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <CoreLocation/CoreLocation.h>

@interface AppDelegate : UIResponder <UIApplicationDelegate, CLLocationManagerDelegate> {
    CLLocationManager *locationManager;
    CLLocation *lastLoc;
    BOOL gpsState;
}

-(void)stopGPSDetect;
-(void)startGPSDetect;

@property (strong, nonatomic) UIWindow *window;

@end
