//
//  AppDelegate.m
//  NTI
//
//  Created by Елена on 05.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "AppDelegate.h"


#define CC_RADIANS_TO_DEGREES(__ANGLE__) ((__ANGLE__) / (float)M_PI * 180.0f)
#define radianConst M_PI/180.0
#define SPEED 1.5

@implementation AppDelegate

@synthesize window = _window, lastLoc, course, trueNorth, north, allDistance, canWriteToFile, dict, recordAction;

#define accelUpdateFrequency 1	



- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions 
{

       
    recordAction = [[RecordAction alloc] init];
    
    [recordAction eventRecord:@"open"]; 
    
    
    
    locationManager=[[CLLocationManager alloc] init];
    locationManager.delegate=self;
    //locationManager.desiredAccuracy=kCLLocationAccuracyBest;
    locationManager.desiredAccuracy= kCLLocationAccuracyNearestTenMeters;//kCLLocationAccuracyBestForNavigation; //
    locationManager.distanceFilter = kCLDistanceFilterNone;
    


    lastLoc = [[CLLocation alloc] init];
    allDistance = 0;
    canWriteToFile = NO;//?
    [recordAction startOfRecord];
    
    [self checkSpeedTimer];
    
           
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
    
    
    [NSTimer scheduledTimerWithTimeInterval:60 target:self selector:@selector(updater:) userInfo:nil repeats:YES];
    
    oldHeading          = 0;
    offsetG             = 0;
    newCompassTarget    = 0;
    
    [self.window makeKeyAndVisible];

    [[UIApplication sharedApplication] setIdleTimerDisabled:YES];
    
    
    [Crittercism initWithAppID:@"4f79a143b093154557000355" 
                        andKey:@"czvj5ewmgoxin8qsxzrjrgnd1y2b" 
                     andSecret:@"yjqmmg4ztbnoodvph29cldqvx3ialn1p" 
         andMainViewController:nil ];
    
    [Crittercism setUsername:[[NSUserDefaults standardUserDefaults] stringForKey:@"login"]];
    
    [TestFlight takeOff:@"14f03353d4c19f3233aafaac63a12ea2_NTAzMTgyMDEyLTAxLTAzIDA4OjMxOjUwLjY1ODIxMg"];
    [TestFlight setDeviceIdentifier:[[NSUserDefaults standardUserDefaults] stringForKey:@"login"]];
   // NSLog(@"UI= %@",[[UIDevice currentDevice] uniqueIdentifier] );
    return YES;
    
}

- (void)checkSpeedTimer{
    [TestFlight passCheckpoint:@"start check speed timer 30 sec"];
    moreThanLimit = NO;
    needCheck = YES;
    m5Km = 0;
    l5Km = 0;
    kmch5 = NO;
    [self startGPSDetect];
    [NSTimer scheduledTimerWithTimeInterval:30 target:self selector:@selector(timerFired:) userInfo:nil repeats:NO];
}

-(void) timerFired: (NSTimer *)timer{
   
    NSLog(@"moreThanLimit = %@", moreThanLimit?@"YES":@"NO");
    NSLog(@"30sec");
    if (!moreThanLimit) {
        [TestFlight passCheckpoint:@"stop check speed timer - NO. stop gps, acc. start 5 min timer"];
        [self stopGPSDetect];
        [self stopMotionDetect];
        canWriteToFile = NO;
        [[NSNotificationCenter defaultCenter]	postNotificationName:	@"canWriteToFile" object:  nil];
        [self fiveMinTimer];
        

    }
    else {
        [self startMotionDetect];
        [TestFlight passCheckpoint:@"stop check speed timer - YES. start recording"];
        needCheck = NO;
        kmch5 = YES;
        canWriteToFile = YES;
        [[NSNotificationCenter defaultCenter]	postNotificationName:	@"canWriteToFile" object:  nil];
        m5Km = 0;
        l5Km = 0;
        NSLog(@"canWriteToFile = YES");
    }
    
}

-(void)fiveMinTimer{
    NSLog(@"5min");
    
    if (kmch5) {
        
        moreThanLimit = NO;
        kmch5 = NO;
        needCheck = YES;
        m5Km = 0;
        l5Km = 0;
        [NSTimer scheduledTimerWithTimeInterval:180 target:self selector:@selector(checkAfterFiveMin) userInfo:nil repeats:NO];
    }
    else [NSTimer scheduledTimerWithTimeInterval:180 target:self selector:@selector(checkSpeedTimer) userInfo:nil repeats:NO];
    
}

-(void)checkAfterFiveMin{
    
    NSLog(@"after 5 min moreThanLimit = %@", moreThanLimit?@"YES":@"NO");
    if (!moreThanLimit) {
        [TestFlight passCheckpoint:@"stop 5 min timer-NO"];
        [self checkSpeedTimer];
        canWriteToFile = NO;
        [[NSNotificationCenter defaultCenter]	postNotificationName:	@"canWriteToFile" object:  nil];
        NSLog(@"canWriteToFile = NO");
        [self checkSendRight];
    }
    else {
        [TestFlight passCheckpoint:@"stop 5 min timer - YES"];
        needCheck = NO;
        kmch5 = YES;
    }

}

-(void)checkSendRight{
    
    if ([[NSUserDefaults standardUserDefaults] integerForKey:@"pk"]>10) {
        if ([ServerCommunication checkInternetConnection])  {
            [TestFlight passCheckpoint:@"checkSendRight: send"];
           [recordAction sendFile];
        }
        
    }
    else  {
        [NSTimer scheduledTimerWithTimeInterval:600 target:self selector:@selector(sendTimer:) userInfo:nil repeats:NO];
        [TestFlight passCheckpoint:@"checkSendRight: start timer"];
    }
    
}

-(void)sendTimer{
    [self checkSendRight];
}



//gps
-(void)stopGPSDetect{
    [TestFlight passCheckpoint:@"stopGPSDetect"];
    [locationManager stopUpdatingLocation];
    [locationManager stopUpdatingHeading];
    gpsState=NO;
}

-(void)startGPSDetect{
    [TestFlight passCheckpoint:@"startGPSDetect"];
    [locationManager startUpdatingLocation];
    [locationManager startUpdatingHeading];
    gpsState=YES;
}

-(double) getTime {
    //[locationManager.location.timestamp timeIntervalSince1970];
    return [locationManager.location.timestamp timeIntervalSince1970];;
}

- (void)locationManager:(CLLocationManager *)manager didUpdateToLocation:(CLLocation *)newLocation fromLocation:(CLLocation *)oldLocation{
    
    
    CLLocationDistance meters = [newLocation distanceFromLocation:oldLocation];
    if (meters<0) meters = 0;
    allDistance += meters;
    
    if (needCheck) {
        if (newLocation.speed > SPEED) {
            m5Km++;
            if (m5Km > 5){
                [TestFlight passCheckpoint:@"location manager: m5km>5, writing"];
                needCheck = NO;
                moreThanLimit = YES;
                canWriteToFile = YES;
                [[NSNotificationCenter defaultCenter]	postNotificationName:	@"canWriteToFile" object:  nil];
            }
        }
    }
    
    if (kmch5) {
        if (newLocation.speed < SPEED){
            l5Km++;
            if (l5Km > 5) {
                [TestFlight passCheckpoint:@"location manager: l5km>5, 5 min timer"];
                [self fiveMinTimer];                
                
            }
        }
        else l5Km = 0;
    }
    

    
    
    lastLoc = [[CLLocation alloc] initWithCoordinate:newLocation.coordinate altitude:newLocation.altitude horizontalAccuracy:newLocation.horizontalAccuracy verticalAccuracy:newLocation.verticalAccuracy course:newLocation.course speed:newLocation.speed timestamp:newLocation.timestamp];
        
    [[NSNotificationCenter defaultCenter]	postNotificationName:	@"locateNotification" object:  nil];
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
    [TestFlight passCheckpoint:@"startMotionDetect"];
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
                                       }];
}

- (void)stopMotionDetect {
    [TestFlight passCheckpoint:@"stopMotionDetect"];
    [motionManager stopDeviceMotionUpdates];
}

- (void)stopRecord{
    [TestFlight passCheckpoint:@"stopRecord"];
    canWriteToFile = NO;
    [[NSNotificationCenter defaultCenter]	postNotificationName:	@"canWriteToFile" object:  nil];
    [self stopGPSDetect];
    [self stopMotionDetect];
}
- (void)startRecord{
    [TestFlight passCheckpoint:@"startRecord"];
    [self startMotionDetect];
    [self checkSpeedTimer];
}




//lifecyrcle for programm							
- (void)applicationWillResignActive:(UIApplication *)application
{
    /*
     Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
     Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
     */
}

- (void)applicationDidEnterBackground:(UIApplication *)application
{
    [TestFlight passCheckpoint:@"background"];
    
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
}

- (void)applicationDidBecomeActive:(UIApplication *)application
{
    
    if ([[NSUserDefaults standardUserDefaults] stringForKey:@"cookie"] == nil){
        [self checkSendRight]; 
    
        UIStoryboard *storyboard = self.window.rootViewController.storyboard;
        UIViewController *loginController = [storyboard instantiateViewControllerWithIdentifier:@"AuthViewController"];
        
        [self.window.rootViewController presentModalViewController:loginController animated:NO];
    }
}

- (void)applicationWillTerminate:(UIApplication *)application
{
    
    NSLog(@"close");
    [recordAction eventRecord:@"close"];
    /*
     Called when the application is about to terminate.
     Save data if appropriate.
     See also applicationDidEnterBackground:.
     */
}





@end
