//
//  AppDelegate.h
//  NTI
//
//  Created by Елена on 05.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <CoreLocation/CoreLocation.h>
#import <CoreMotion/CoreMotion.h>

@interface AppDelegate : UIResponder <UIApplicationDelegate, CLLocationManagerDelegate, UIAccelerometerDelegate> {
    CLLocationManager *locationManager;
    CLLocation *lastLoc;
    BOOL gpsState;
    
    CMMotionManager *motionManager;
    
    //for compass
    NSOperationQueue    *opQ;
    
    float course;
    float updatedHeading;
    float northOffest;
    float oldHeading;
    float offsetG;
    float newCompassTarget;
    float currentYaw;

}

-(void)stopGPSDetect;
-(void)startGPSDetect;

//- (void)stopAccelerometerDetect;
//- (void)startAccelerometerDetect;

-(void) startMotionDetect;

-(double) getTime;

@property (strong, nonatomic) UIWindow *window;
@property (readonly, NS_NONATOMIC_IPHONEONLY) CLLocation *lastLoc;
@property (nonatomic) float course;
@property (nonatomic) float trueNorth;

@end
