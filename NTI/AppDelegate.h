//
//  AppDelegate.h
//  NTI
//
//  Created by Елена on 05.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "Crittercism.h"
#import <UIKit/UIKit.h>
#import <CoreLocation/CoreLocation.h>
#import <CoreMotion/CoreMotion.h>
#import "AuthViewController.h"
#import "DatabaseActions.h"
#import "RecordAction.h"



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
    BOOL moreThanLimit;
   // NSDictionary *dict;
    RecordAction *recordAction;
    DatabaseActions *databaseAction;
    BOOL needCheck;
    
    NSTimer *timer30sec;
    NSTimer *timer5min;
    NSTimer *sendTimer;
}

-(void)stopGPSDetect;
-(void)startGPSDetect;

-(void)checkSendRight;

//- (void)stopAccelerometerDetect;
//- (void)startAccelerometerDetect;

-(void) startMotionDetect;
-(void) stopMotionDetect;
-(void) checkSpeedTimer;
-(double) getTime;
-(void) timerFired: (NSTimer *)timer;
- (void)fiveMinTimer;
- (void)checkAfterFiveMin;
- (void)stopRecord;
- (void)startRecord;
- (void)sendTimer;


@property (strong, nonatomic) UIWindow *window;
@property (readonly, NS_NONATOMIC_IPHONEONLY) CLLocation *lastLoc;
@property (nonatomic) float course;
@property (nonatomic) float trueNorth;
@property (nonatomic) float north;
@property (readonly, nonatomic) CLLocationDistance allDistance;
@property (nonatomic) BOOL canWriteToFile;
@property (strong, nonatomic) NSDictionary *dict;
@property (nonatomic,strong) RecordAction *recordAction;



@end
