//
//  AppDelegate.m
//  NTI
//
//  Created by Елена on 05.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "AppDelegate.h"

@implementation AppDelegate

@synthesize window = _window, lastLoc;

#define accelUpdateFrequency 30.0	

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions 
{
    locationManager=[[CLLocationManager alloc] init];
    locationManager.delegate=self;
    locationManager.desiredAccuracy=kCLLocationAccuracyBest;
    
    lastLoc = [[CLLocation alloc] init];
    [self startGPSDetect];
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

    return YES;
}



//gps
-(void)stopGPSDetect{
    [locationManager stopUpdatingLocation];
    gpsState=NO;
}

-(void)startGPSDetect{
    [locationManager startUpdatingLocation];
    gpsState=YES;
}

-(double) getTime {
    //[locationManager.location.timestamp timeIntervalSince1970];
    return [locationManager.location.timestamp timeIntervalSince1970];;
}

- (void)locationManager:(CLLocationManager *)manager didUpdateToLocation:(CLLocation *)newLocation fromLocation:(CLLocation *)oldLocation{
    
    lastLoc = [[CLLocation alloc] initWithCoordinate:newLocation.coordinate altitude:newLocation.altitude horizontalAccuracy:newLocation.horizontalAccuracy verticalAccuracy:newLocation.verticalAccuracy course:newLocation.course speed:newLocation.speed timestamp:newLocation.timestamp];
        
    [[NSNotificationCenter defaultCenter]	postNotificationName:	@"locateNotification" object:  nil];
}

//motion

-(void) startMotionDetect{
    [motionManager startDeviceMotionUpdatesToQueue:[NSOperationQueue currentQueue] 
                                       withHandler:^(CMDeviceMotion *motion, NSError *error) {
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
