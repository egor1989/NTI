//
//  AppDelegate.m
//  NTI
//
//  Created by Елена on 05.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "AppDelegate.h"
#import "FileController.h"
#import "ServerCommunication.h"


#define CC_RADIANS_TO_DEGREES(__ANGLE__) ((__ANGLE__) / (float)M_PI * 180.0f)
#define radianConst M_PI/180.0
#define SPEED 1.5
#define STARTTIME 300 //!!
#define STOPTIME 600 //!!


@implementation AppDelegate
@synthesize window = _window, lastLoc, course, trueNorth, north, allDistance, canWriteToFile, dict, recordAction, locationUpdatedInBackground;


- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions 
{  
    freopen([[FileController filePath] cStringUsingEncoding:NSASCIIStringEncoding],"a+",stderr);      //!!!!!не забывать убирать логирвоание
    [self appLife:@"on" time: [NSString stringWithFormat:@"%.f", [[NSDate date]timeIntervalSince1970]]];
    
    locationManager = [[CLLocationManager alloc] init];
    [[UIApplication sharedApplication] registerForRemoteNotificationTypes: (UIRemoteNotificationTypeAlert |UIRemoteNotificationTypeBadge |UIRemoteNotificationTypeSound)];
 /***********************************************************************************/   
    if ([launchOptions objectForKey:UIApplicationLaunchOptionsLocationKey]) {
        //тестовый блок, будет показывать local notification с координатами
             UILocalNotification *notification = [[UILocalNotification alloc] init];
             notification.fireDate = [NSDate dateWithTimeIntervalSinceNow:15];
             notification.alertBody = [NSString stringWithFormat:@"NTI. New location alert"];
             [[UIApplication sharedApplication] scheduleLocalNotification:notification];
        
      //  if ([ServerCommunication checkInternetConnection: NO]) {
            [ServerCommunication sendNotification:[NSString stringWithFormat:@"%.0f",[[[NSDate alloc ]init]timeIntervalSince1970]] lng:[NSString stringWithFormat:@"%.6f",locationManager.location.coordinate.longitude] lat:[NSString stringWithFormat:@"%.6f",locationManager.location.coordinate.latitude]];
       // }
    //    else {
            //if ([[NSUserDefaults standardUserDefaults] objectForKey:@"nArray"]==nil) {
            //    
            //}
     //   }

         //[locationManager startUpdatingLocation];
         //[locationManager startMonitoringSignificantLocationChanges];
        NSLog(@"NOTIFICATION");
    }
 /***********************************************************************************/  
    
    recordAction = [[RecordAction alloc] init];
    
    locationManager.delegate=self;
    locationManager.desiredAccuracy= kCLLocationAccuracyNearestTenMeters;
    locationManager.distanceFilter = kCLDistanceFilterNone;
    
    
    lastLoc = [[CLLocation alloc] init];
    allDistance = 0;
    canWriteToFile = NO;//переделать на NO!!
    slowMonitoring = NO;
    [recordAction startOfRecord];
    needCheck = YES;
    
    startCheck = YES;
    firstTimer = [NSTimer scheduledTimerWithTimeInterval:STARTTIME target:self selector:@selector(finishFirstTimer) userInfo:nil repeats:NO];
    
    [self checkSendRight];
    [self startGPSDetect];

    motionManager = [[CMMotionManager alloc] init];
    if ([motionManager isGyroAvailable]) {
        motionManager.deviceMotionUpdateInterval = 1.0;//регулировка частоты
        
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
    return YES;
    
}

-(void)checkSendRight{
    
    if ([[NSUserDefaults standardUserDefaults] integerForKey:@"pk"]>30) {//!!!исправить на 30
        if ([ServerCommunication checkInternetConnection: YES])  {
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
    motionManager.deviceMotionUpdateInterval = 0.1;
    slowMonitoring = YES;
    startCheck = NO;
}


//gps
-(void)stopGPSDetect{
   
    [locationManager startMonitoringSignificantLocationChanges];
    NSLog(@"startMonitoringSignificantLocationChanges");
    
    [locationManager stopUpdatingLocation];
    [locationManager stopUpdatingHeading];
    NSLog(@"stopGPSDetect");
    startCheck = NO;
}

-(void)startGPSDetect{
    
    [locationManager startUpdatingLocation];
    NSLog(@"startGPSDetect");
    [locationManager startUpdatingHeading];
    
    [locationManager stopMonitoringSignificantLocationChanges];
    NSLog(@"stopMonitoringSignificantLocationChange");

}

-(double) getTime {

    return [locationManager.location.timestamp timeIntervalSince1970];;
}

- (void)locationManager:(CLLocationManager *)manager didUpdateToLocation:(CLLocation *)newLocation fromLocation:(CLLocation *)oldLocation{
    
    [TestFlight takeOff:@"14f03353d4c19f3233aafaac63a12ea2_NTAzMTgyMDEyLTAxLTAzIDA4OjMxOjUwLjY1ODIxMg"];
    
    CLLocationDistance meters = [newLocation distanceFromLocation:oldLocation];
    if (meters<0) meters = 0;
    allDistance += meters;
    
    //строчка ниже нужна только в случае тестирования, чтобы данные набирались
    //[[NSNotificationCenter defaultCenter]	postNotificationName:	@"canWriteToFile" object:  nil];//!!УБРАТЬ
    
    //только включили приложение, начальная проверка
    // также работает когда сработала смена вышки
    //в этих случаях startCheck = YES
    if (startCheck) {
        NSLog(@"startCheck");
         //если в течении 5 минут мы собрали 5 >5км/ч начинаем запись
        if (newLocation.speed > SPEED) {
            m5Km++;
            if (m5Km > 5){
                NSLog(@"startCheck-location manager: m5km>5, writing");
                //[recordAction eventRecord:@"start"];
                startCheck = NO;
                //все теперь сюда заходить не будет
                [firstTimer invalidate];
                //обновили до зеленого значка
                canWriteToFile = YES;
                m5Km = 0;
                [[NSNotificationCenter defaultCenter]	postNotificationName:	@"canWriteToFile" object:  nil];
            }
        } 
        //если не подряд события то обнуляется
        else m5Km = 0;
       
        //не было движения - переходим в медленный режим - finishFirstTimer
    }
    
    //приложение уже работало - медленный режим - ждет смену вышки
    else if (slowMonitoring){
        //сработала смена локации по вышке
        NSLog(@"slowMonitoring - change location");
        //включили gps 
        //увеличили частоту обновление акселерометра - его выключать нельзя так как он в фоне потом не включается - поэтому лучше делать меньше частоту
        [self startGPSDetect];
        
        UILocalNotification *notification = [[UILocalNotification alloc] init];
        notification.fireDate = [NSDate dateWithTimeIntervalSinceNow:15];
        notification.alertBody = [NSString stringWithFormat:@"NTI-BACKGROUND. New location alert"];
        [[UIApplication sharedApplication] scheduleLocalNotification:notification];
        
        
        motionManager.deviceMotionUpdateInterval = 1.0;
        //включили опять опции как в начальной проверке, а не сразу включили запись
        //если не будет работать, то убрать эти три строчки ниже И раскомментить те две которые сейчас закомменчены canWrite..=YES, notification
        startCheck = YES;
        NSLog(@"start timer");
        firstTimer = [NSTimer scheduledTimerWithTimeInterval:STARTTIME target:self selector:@selector(finishFirstTimer) userInfo:nil repeats:NO];
        //canWriteToFile = YES;
        //[[NSNotificationCenter defaultCenter]	postNotificationName:	@"canWriteToFile" object:  nil];
        slowMonitoring = NO;
    }
    
    //gps
    else {
        //работает таймер на стоп
        if ([stopTimer isValid]) {
            if (newLocation.speed > SPEED) {
                m5Km++;
                //3 события подряд больше 5 км/ч
                if (m5Km > 3){
                    NSLog(@"stopTimer-location manager: m5km>5, finish stopTimer");
                    //так как у нас опять движение выключаем таймер
                    [stopTimer invalidate];
                    m5Km = 0;
                }
            } else m5Km = 0;
        }
        //если записывает а скорость 5 раз меньше 5 км/ч
       else if (newLocation.speed < SPEED){
            l5Km++;
           //5 событий подряд меньше 5 км/ч
            if (l5Km > 5) {
                //включили таймер который проверяет на стоп
                stopTimer = [NSTimer scheduledTimerWithTimeInterval:STOPTIME target:self selector:@selector(finishStopTimer) userInfo:nil repeats:NO];
                l5Km = 0;
                NSLog(@"l5Km>5, start stopTimer");
            }
        }
       else {
           l5Km = 0;
           canWriteToFile = YES;
       }
    }
    
    
    lastLoc = [[CLLocation alloc] initWithCoordinate:newLocation.coordinate altitude:newLocation.altitude horizontalAccuracy:newLocation.horizontalAccuracy verticalAccuracy:newLocation.verticalAccuracy course:newLocation.course speed:newLocation.speed timestamp:newLocation.timestamp];
        
    [[NSNotificationCenter defaultCenter]	postNotificationName:	@"locateNotification" object:  nil];
   // NSLog(@"locateNotification");
     
}

- (void)finishStopTimer{
    NSLog(@"finish stop timer");
    slowMonitoring = YES;
    [self stopGPSDetect];
    //[self stopMotionDetect];
    motionManager.deviceMotionUpdateInterval = 0.1;
    [self checkSendRight];
    canWriteToFile = NO;
    [[NSNotificationCenter defaultCenter]	postNotificationName:	@"canWriteToFile" object:  nil];
}




//compass

- (void)locationManager:(CLLocationManager *)manager didUpdateHeading:(CLHeading *)newHeading
{
    // Update variable updateHeading to be used in updater method
    updatedHeading = newHeading.magneticHeading;
    trueNorth = 0 - newHeading.trueHeading;
    north = 360 - newHeading.trueHeading;

    course = (int)((360+trueNorth)+lastLoc.course) % 360; 
    
    
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
                                       }];
}

- (void)stopMotionDetect {
    NSLog(@"stopMotionDetect");
    [motionManager stopDeviceMotionUpdates];
}

- (void)stopSlowMonitoring{
    NSLog(@"STOP SLOW MONITORING");
    [locationManager stopMonitoringSignificantLocationChanges];
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

    //motionManager.deviceMotionUpdateInterval = 0.1;
    //[self startMotionDetect];
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
    [locationManager startMonitoringSignificantLocationChanges];
    //[recordAction eventRecord:@"close"];
    [DatabaseActions finalizeStatements];
    [self appLife:@"off" time: [NSString stringWithFormat:@"%.f", [[NSDate date]timeIntervalSince1970]]];
    NSLog(@"=====close=====");
}


- (void)application:(UIApplication *)application didReceiveLocalNotification:(UILocalNotification *)notification {
    
    NSLog(@"LocalNotification - App in foreground");

    //[[UIApplication sharedApplication] cancelAllLocalNotifications]; 
    application.applicationIconBadgeNumber = 0;
}

- (BOOL)textFieldShouldReturn:(UITextField *)textField{
    return NO;
}

- (void)appLife:(NSString *)action time:(NSString *)time{
    
    NSDictionary *appInfo = [NSDictionary dictionaryWithObjects:[NSArray arrayWithObjects:action,time, nil] forKeys:[NSArray arrayWithObjects:@"action", @"time", nil]];
    NSMutableArray *lifeArray = [NSMutableArray arrayWithArray: [[NSUserDefaults standardUserDefaults] objectForKey:@"alArray"]];
    
    [lifeArray addObject: appInfo];
    NSLog(@"%i  %@", [lifeArray count], lifeArray);
    

    
    if ([ServerCommunication checkInternetConnection: NO]){
        for (NSInteger i = 0; i<[lifeArray count]; i++) [ServerCommunication sendAppInfo: lifeArray];
        [lifeArray removeAllObjects];
    }
    [[NSUserDefaults standardUserDefaults] setObject: lifeArray forKey:@"alArray"];
    [[NSUserDefaults standardUserDefaults] synchronize];

}

- (void)application:(UIApplication*)application didRegisterForRemoteNotificationsWithDeviceToken:(NSData*)deviceToken {  
    NSLog(@"%@",deviceToken);
}





@end
