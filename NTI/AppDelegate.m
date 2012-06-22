//
//  AppDelegate.m
//  NTI
//
//  Created by Елена on 05.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "AppDelegate.h"
#import "FileController.h"


#define CC_RADIANS_TO_DEGREES(__ANGLE__) ((__ANGLE__) / (float)M_PI * 180.0f)
#define radianConst M_PI/180.0
#define SPEED 1.5
#define STARTTIME 30
#define STOPTIME 60

@implementation AppDelegate

@synthesize window = _window, lastLoc, course, trueNorth, north, allDistance, canWriteToFile, dict, recordAction;

#define accelUpdateFrequency 1	



- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions 
{  
//    recordAction = [[RecordAction alloc] init];
//    
//    [recordAction eventRecord:@"open"]; 
    
   // freopen([[FileController filePath] cStringUsingEncoding:NSASCIIStringEncoding],"a+",stderr); //!!!!!не забывать убирать логирвоание
    
    locationManager=[[CLLocationManager alloc] init];
    locationManager.delegate=self;
    locationManager.desiredAccuracy= kCLLocationAccuracyNearestTenMeters;
    locationManager.distanceFilter = kCLDistanceFilterNone;
    
    lastLoc = [[CLLocation alloc] init];
    allDistance = 0;
    canWriteToFile = NO;//?
    slowMonitoring = NO;
    [recordAction startOfRecord];
    needCheck = YES;
    
    startCheck = YES;
    firstTimer = [NSTimer scheduledTimerWithTimeInterval:STARTTIME target:self selector:@selector(finishFirstTimer) userInfo:nil repeats:NO];
    
    [self checkSendRight];
    [self startGPSDetect];
    //пока motion отключен
    //[self stopMotionDetect];
    motionManager = [[CMMotionManager alloc] init];
    if ([motionManager isGyroAvailable]) {
        motionManager.deviceMotionUpdateInterval = 1.0/accelUpdateFrequency;
        [self startMotionDetect];
    }
    else{
//    motionManager.accelerometerUpdateInterval = 1.0 / accelUpdateFrequency;
//    [self startAccelerometerDetect];
        NSLog(@"bad iphone");
    }
    
    //
    [NSTimer scheduledTimerWithTimeInterval:60 target:self selector:@selector(updater:) userInfo:nil repeats:YES];
    
    oldHeading          = 0;
    offsetG             = 0;
    newCompassTarget    = 0;
    
        
    
    [Crittercism initWithAppID:@"4f79a143b093154557000355" 
                        andKey:@"czvj5ewmgoxin8qsxzrjrgnd1y2b" 
                     andSecret:@"yjqmmg4ztbnoodvph29cldqvx3ialn1p" 
         andMainViewController:nil ];
    
    [Crittercism setUsername:[[NSUserDefaults standardUserDefaults] stringForKey:@"login"]];
    
    [self.window makeKeyAndVisible];
    
    [[UIApplication sharedApplication] setIdleTimerDisabled:YES];
    return YES;
    
}

-(void)checkSendRight{
    
    if ([[NSUserDefaults standardUserDefaults] integerForKey:@"pk"]>10) {//!!!исправить на 10
        if ([ServerCommunication checkInternetConnection])  {
            NSLog(@"checkSendRight: send");
           [recordAction sendFile];
        }
        else  {
            sendTimer = [NSTimer scheduledTimerWithTimeInterval:600 target:self selector:@selector(sendTimer:) userInfo:nil repeats:NO];
            NSLog(@"checkSendRight: start timer");
        }
    }
}

-(void)sendTimer{
    [self checkSendRight];
}

- (void)finishFirstTimer{
    NSLog(@"finishFirstTimer");
    [self stopGPSDetect];
    
    slowMonitoring = YES;
    startCheck = NO;
}


//gps
-(void)stopGPSDetect{
    NSLog(@"stopGPSDetect");
    [locationManager stopUpdatingLocation];
    [locationManager stopUpdatingHeading];
    [locationManager startMonitoringSignificantLocationChanges];
    NSLog(@"startMonitoringSignificantLocationChanges");

}

-(void)startGPSDetect{
    NSLog(@"startGPSDetect");
    [locationManager stopMonitoringSignificantLocationChanges];
    NSLog(@"stopMonitoringSignificantLocationChange");
    [locationManager startUpdatingLocation];
    [locationManager startUpdatingHeading];

}

-(double) getTime {

    return [locationManager.location.timestamp timeIntervalSince1970];;
}

- (void)locationManager:(CLLocationManager *)manager didUpdateToLocation:(CLLocation *)newLocation fromLocation:(CLLocation *)oldLocation{
    
    CLLocationDistance meters = [newLocation distanceFromLocation:oldLocation];
    if (meters<0) meters = 0;
    allDistance += meters;
    
    //только включили приложение, начальная проверка
    if (startCheck) {
        NSLog(@"startCheck");
         //если в течении 5 минут мы собрали 5 >5км/ч начинаем запись
        if (newLocation.speed > SPEED) {
            m5Km++;
            if (m5Km > 5){
                NSLog(@"startCheck-location manager: m5km>5, writing");
                //[recordAction eventRecord:@"start"];
                startCheck = NO;
                [firstTimer invalidate];
                canWriteToFile = YES;
                m5Km = 0;
                [[NSNotificationCenter defaultCenter]	postNotificationName:	@"canWriteToFile" object:  nil];
            }
        } else m5Km = 0;
       
        //нет переходим в медленный режим - finishFirstTimer
    }
    //приложение уже работало - медленный режим
    else if (slowMonitoring){
        NSLog(@"slowMonitoring - change location");
        [self startGPSDetect];
        canWriteToFile = YES;
        //[recordAction eventRecord:@"start"];
        [[NSNotificationCenter defaultCenter]	postNotificationName:	@"canWriteToFile" object:  nil];
        slowMonitoring = NO;
    }
    //gps
    else {
        //работает таймер на стоп
        if ([stopTimer isValid]) {
            if (newLocation.speed > SPEED) {
                m5Km++;
                if (m5Km > 5){
                    NSLog(@"stopTimer-location manager: m5km>5, finish stopTimer");
                    [stopTimer invalidate];
                    m5Km = 0;
                }
            } else m5Km = 0;
        }
       else if (newLocation.speed < SPEED){
            l5Km++;
            if (l5Km > 5) {
                stopTimer = [NSTimer scheduledTimerWithTimeInterval:STOPTIME target:self selector:@selector(finishStopTimer) userInfo:nil repeats:NO];
                l5Km = 0;
                NSLog(@"l5Km>5, start stopTimer");
            }
        }
        else l5Km = 0;
    }
    
    
    lastLoc = [[CLLocation alloc] initWithCoordinate:newLocation.coordinate altitude:newLocation.altitude horizontalAccuracy:newLocation.horizontalAccuracy verticalAccuracy:newLocation.verticalAccuracy course:newLocation.course speed:newLocation.speed timestamp:newLocation.timestamp];
        
    [[NSNotificationCenter defaultCenter]	postNotificationName:	@"locateNotification" object:  nil];
   // NSLog(@"locateNotification");
     
}

- (void)finishStopTimer{
    NSLog(@"finish stop timer");
    slowMonitoring = YES;
    [self stopGPSDetect];
    [self checkSendRight];
    canWriteToFile = NO;
    [[NSNotificationCenter defaultCenter]	postNotificationName:	@"canWriteToFile" object:  nil];
    
    
}

//compass

- (void)calibrate:(NSTimer *)timer
{   
    // Set offset so the compassImg will be calibrated to northOffset
    northOffest = updatedHeading - 0;
}

- (void)updater:(NSTimer *)timer 
{
   // northOffest = updatedHeading - 0; кнопка калибровки

    // If the compass hasn't moved in a while we can calibrate the gyro 
    if(updatedHeading == oldHeading) {
       // NSLog(@"Update gyro");
        // Populate newCompassTarget with new compass value and the offset we set in calibrate
        newCompassTarget = (0 - updatedHeading) + northOffest;
        
        offsetG = currentYaw;
    } 
    
    oldHeading = updatedHeading;
}



- (void)locationManager:(CLLocationManager *)manager didUpdateHeading:(CLHeading *)newHeading
{
    // Update variable updateHeading to be used in updater method
    updatedHeading = newHeading.magneticHeading;
    trueNorth = 0 - newHeading.trueHeading;
    north = 360 - newHeading.trueHeading;
        
    //compassImg.transform = CGAffineTransformMakeRotation((headingFloat + northOffest)*radianConst); 
    //course = (headingFloat + northOffest)*radianConst;
    //NSLog(@"%f north", northOffest);
    course = (int)((360+trueNorth)+lastLoc.course) % 360; //mod 360;
    
    
    
    
    
    //trueNorth.transform = CGAffineTransformMakeRotation(headingFloat*radianConst);
    [[NSNotificationCenter defaultCenter]	postNotificationName:	@"redrawCourse" object:  nil];
}





//motion

-(void) startMotionDetect{
    NSLog(@"startMotionDetect");
    [motionManager startDeviceMotionUpdatesToQueue:[NSOperationQueue currentQueue] 
                                       withHandler:^(CMDeviceMotion *motion, NSError *error) {
                                           CMAttitude *currentAttitude = motion.attitude;
                                           float yawValue = currentAttitude.yaw; // Use the yaw value
                                           
                                           // Yaw values are in radians (-180 - 180), here we convert to degrees
                                           float yawDegrees = CC_RADIANS_TO_DEGREES(yawValue);
                                           currentYaw = yawDegrees;
                                           
                                           // We add new compass value together with new yaw value
                                           yawDegrees = newCompassTarget + (yawDegrees - offsetG);
                                           
                                           // Degrees should always be positive
                                           if(yawDegrees < 0) {
                                               yawDegrees = yawDegrees + 360;

                                           }
                                          dict = [NSDictionary dictionaryWithObject: motion forKey: @"motion"];
                                           [[NSNotificationCenter defaultCenter] postNotificationName: @"motionNotification" object:  nil];
                                          // NSLog(@"motionNotification");
                                       }];
}

- (void)stopMotionDetect {
    NSLog(@"stopMotionDetect");
    [motionManager stopDeviceMotionUpdates];
}

- (void)stopRecord{
    NSLog(@"stopRecord");
    canWriteToFile = NO;
    [[NSNotificationCenter defaultCenter]	postNotificationName:	@"canWriteToFile" object:  nil];
    [self stopGPSDetect];
    [self stopMotionDetect];
}
- (void)startRecord{
    NSLog(@"startRecord");
    [self startMotionDetect];
    //[self checkSpeedTimer];
}




//lifecyrcle for programm							
- (void)applicationWillResignActive:(UIApplication *)application
{
    NSLog(@"=====application will resign active (call or sms or background)=====");
    /*
     Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
     Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
     */
}

- (void)applicationDidEnterBackground:(UIApplication *)application
{
    NSLog(@"=====background=====");
    
        if ([firstTimer isValid]){
        NSLog(@"work first timer");
    }
    if ([shortCheckTimer isValid]) [shortCheckTimer invalidate];
    
    /*
     Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later. 
        If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
     */
}

- (void)applicationWillEnterForeground:(UIApplication *)application
{
    /*
     Called as part of the transition from the background to the inactive state; here you can undo many of the changes made on entering the background.
     */
    
    
    NSLog(@"=====foreground=====");
    
    if ([firstTimer isValid]) {
        NSLog(@"firstTimer working");
    }
    else if ([stopTimer isValid]) {
        NSLog(@"stopTimer working");
    }
    else 
        if (!canWriteToFile) {
        NSLog(@"startShortCheckTimer");
        startCheck = YES;
        shortCheckTimer = [NSTimer scheduledTimerWithTimeInterval:60 target:self selector:@selector(finishShortCheckTimer) userInfo:nil repeats:NO];
    }
    
    [self startMotionDetect];
}

- (void)finishShortCheckTimer{
    NSLog(@"StopShortCheckTimer");
    startCheck = NO;
}

- (void)applicationDidBecomeActive:(UIApplication *)application
{
    
    if ([[NSUserDefaults standardUserDefaults] stringForKey:@"cookie"] == nil){
        
        
        UIStoryboard *storyboard = self.window.rootViewController.storyboard;
        UIViewController *loginController = [storyboard instantiateViewControllerWithIdentifier:@"AuthViewController"];
        NSLog(@"=====didBecomeActive=====");
        [self.window.rootViewController presentModalViewController:loginController animated:NO];
    }
}

- (void)applicationWillTerminate:(UIApplication *)application
{
    [recordAction eventRecord:@"close"];
    [locationManager stopMonitoringSignificantLocationChanges];
    NSLog(@"=====close=====");
}





@end
