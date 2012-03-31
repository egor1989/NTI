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
#import "AuthViewController.h"
#import "recordAction.h"


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
    CLLocationDistance allDistance;
    BOOL kmch5;
    NSInteger l5Km;
    NSInteger m5Km;
    BOOL canWriteToFile;

}

-(void)stopGPSDetect;
-(void)startGPSDetect;

//- (void)stopAccelerometerDetect;
//- (void)startAccelerometerDetect;

-(void) startMotionDetect;
- (void)checkSpeedTimer;
-(double) getTime;
-(void) timerFired: (NSTimer *)timer;
-(void)fiveMinTimer;
-(void)checkAfterFiveMin;

@property (strong, nonatomic) UIWindow *window;
@property (readonly, NS_NONATOMIC_IPHONEONLY) CLLocation *lastLoc;
@property (nonatomic) float course;
@property (nonatomic) float trueNorth;
@property (nonatomic) float north;
@property (readonly, nonatomic) CLLocationDistance allDistance;
@property (nonatomic) BOOL canWriteToFile;



@end
