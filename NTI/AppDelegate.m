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

@synthesize window = _window, lastLoc, course, trueNorth, north, allDistance, canWriteToFile;

#define accelUpdateFrequency 5.0	

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions 
{
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    
    
        //[[UIApplication sharedApplication] setStatusBarStyle:UIStatusBarStyleBlackOpaque animated:YES];
    
    locationManager=[[CLLocationManager alloc] init];
    locationManager.delegate=self;
    //locationManager.desiredAccuracy=kCLLocationAccuracyBest;
    locationManager.desiredAccuracy= kCLLocationAccuracyNearestTenMeters;//kCLLocationAccuracyBestForNavigation; //
    locationManager.distanceFilter = kCLDistanceFilterNone;
    

    
    lastLoc = [[CLLocation alloc] init];
    kmch5 = NO;
    l5Km = 0;
    m5Km = 0;
    allDistance = 0;
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
    
    
    [NSTimer scheduledTimerWithTimeInterval:2 target:self selector:@selector(updater:) userInfo:nil repeats:YES];
    //[NSTimer scheduledTimerWithTimeInterval:2 target:self selector:@selector(calibrate:) userInfo:nil repeats:YES];
    
    oldHeading          = 0;
    offsetG             = 0;
    newCompassTarget    = 0;
    FirstViewController *firstViewController = [[FirstViewController alloc] init];
    //UIStoryboard *storyboard = [UIStoryboard storyboardWithName:@"MainStoryboard" bundle:nil];
    //firstViewController = [storyboard instantiateViewControllerWithIdentifier:@"authAndRegView"];
    if ([userDefaults stringForKey:@"login"] == nil)  {
       // _window.rootViewController = firstViewController;
        //[_window addSubview:firstViewController.view];
        //[self.window setRootViewController: firstViewController];
        
        [self.window.rootViewController performSegueWithIdentifier:@"authAndRegView" sender:self];    
    }
    //else self.window.rootViewController = self.tabBarController;
    
    [self.window makeKeyAndVisible];
    

    return YES;
    
}

- (void)checkSpeedTimer{
    [NSTimer scheduledTimerWithTimeInterval:30 target:self selector:@selector(timerFired:) userInfo:nil repeats:NO];

    [self startGPSDetect];
}

-(void) timerFired: (NSTimer *)timer{
    NSLog(@"m=%i l=%i", m5Km,l5Km);
    NSLog(@"30sec");
    if (l5Km>m5Km) {
        [self stopGPSDetect];
        [self fiveMinTimer];

    }
    else {
        [self startGPSDetect];
        kmch5 = YES;
        canWriteToFile = YES;
        NSLog(@"canWriteToFile = YES");
    }
    
}

-(void)fiveMinTimer{
    NSLog(@"5min");
    if (kmch5) {
        l5Km = 0;
        m5Km = 0;
        kmch5 = NO;
        [NSTimer scheduledTimerWithTimeInterval:300 target:self selector:@selector(checkAfterFiveMin) userInfo:nil repeats:NO];
    }
    else [NSTimer scheduledTimerWithTimeInterval:300 target:self selector:@selector(checkSpeedTimer) userInfo:nil repeats:NO];
}

-(void)checkAfterFiveMin{
    NSLog(@"after 5 min m=%i l=%i", m5Km,l5Km);
    if (l5Km>m5Km) {
        [self checkSpeedTimer];
        canWriteToFile = NO;
        NSLog(@"canWriteToFile = NO");
    }
    else kmch5 = YES;
}



//gps
-(void)stopGPSDetect{
    NSLog(@"stopGPSDetect");
    [locationManager stopUpdatingLocation];
    [locationManager stopUpdatingHeading];
    gpsState=NO;
}

-(void)startGPSDetect{
    NSLog(@"startGPSDetect");
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
    if (newLocation.speed > SPEED) m5Km++;
    else l5Km++;
    
    if (kmch5) 
        if (newLocation.speed < SPEED) [self fiveMinTimer];
    
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
                                           NSDictionary* dict = [NSDictionary dictionaryWithObject: motion
                                                                                            forKey: @"motion"];
                                           [[NSNotificationCenter defaultCenter]	postNotificationName:	@"motionNotification" 
                                                                                               object:  nil
                                                                                             userInfo:dict];
                                       }];
}

//accelerometer
//- (void)stopAccelerometerDetect {
//    [motionManager stopAccelerometerUpdates];
//}
//
//- (void)startAccelerometerDetect
//{
//    [motionManager startAccelerometerUpdatesToQueue:[[NSOperationQueue alloc] init]
//                                        withHandler:^(CMAccelerometerData *data, NSError *error) {
//                                            dispatch_async(dispatch_get_main_queue(), ^{
//                                                NSDictionary* accDict = [NSDictionary dictionaryWithObject: data
//                                                                                                    forKey: @"accel"];
//                                                [[NSNotificationCenter defaultCenter]	postNotificationName:@"accelNotification" 
//                                                                                                    object:  nil
//                                                                                                  userInfo:accDict];
//                                            });
//                                        }
//     ];
//}
//



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
    /*
     Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
     */
}

- (void)applicationWillTerminate:(UIApplication *)application
{
    /*
     Called when the application is about to terminate.
     Save data if appropriate.
     See also applicationDidEnterBackground:.
     */
}

@end
