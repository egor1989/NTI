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
    RecordAction *recordAction;
    DatabaseActions *databaseAction;

    BOOL kmch5;
    NSInteger l5Km;
    NSInteger m5Km;
    BOOL canWriteToFile;
    BOOL moreThanLimit;

 //   BOOL lowAccuracy; //низкая точность

    BOOL checkMotion;
     
  //  BOOL startCheck; // только включили приложение
  //  BOOL slowMonitoring; //детектирование по вышкам
    
    NSTimer *stopTimer;
    NSTimer *sendTimer;
    NSTimer *firstTimer;
    NSTimer *shortCheckTimer;
}

- (void)stopGPSDetect;
- (void)startGPSDetect;
- (void)finishShortCheckTimer;
- (void)checkSendRight;

-(void)finishFirstTimer;
-(void)finishStopTimer;
//- (void)stopAccelerometerDetect;
//- (void)startAccelerometerDetect;

-(void) startMotionDetect;
-(void) stopMotionDetect;
-(double) getTime;

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
