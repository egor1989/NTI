//
//  RecordAction.m
//  NTI
//
//  Created by Елена on 07.04.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "RecordAction.h"
#import "AppDelegate.h"
#define myAppDelegate (AppDelegate*) [[UIApplication sharedApplication] delegate]
#define MAX3(a,b,c) ( MAX(a,b)>c ? ((a>b)? 1:2) : 3 )
#define radianConst M_PI/180.0
#define maxEntries 10


@implementation RecordAction

- (id)init{
    databaseAction = [[DatabaseActions alloc] initDataBase];
    
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(checkWriteRight)
     name: @"motionNotification"
     object: nil];
    
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(checkWriteRight)
     name: @"canWriteToFile"
     object: nil];
    
    return self;
}

- (void)startOfRecord{
    dataArray = [[NSMutableArray alloc] init];
}

- (void)addRecord{
    accelDict = [myAppDelegate dict];
    
    CMAcceleration userAcceleration=((CMDeviceMotion*)[accelDict objectForKey: @"motion"]).userAcceleration;
    CMAcceleration gravity=((CMDeviceMotion*)[accelDict objectForKey: @"motion"]).gravity;
    int maxGravityAxe = MAX3(fabs(gravity.x), fabs(gravity.y), fabs(gravity.z));
    
    switch (maxGravityAxe) {
        case 1:
            x=-userAcceleration.z;
            y=userAcceleration.y;
            break;
        case 2:
            x=-userAcceleration.x;
            y=-userAcceleration.z;
            break;
        case 3:
            x=userAcceleration.x;
            y=userAcceleration.y;
            break;
    }
        
    CLLocation *location = [myAppDelegate lastLoc];
    
    float curSpeed = 0;
    if (location.speed > 0) curSpeed = location.speed*3.6;
    float distance = [myAppDelegate allDistance]/1000;
    NSString *type = @"-";

    NSArray *keys = [NSArray arrayWithObjects:@"timestamp", @"type", @"accX", @"accY", @"compass", @"direction", @"distance", @"latitude", @"longitude",@"speed", nil];
    
    NSArray *objs = [NSArray arrayWithObjects:  [NSString stringWithFormat:@"%.0f",[[[NSDate alloc ]init]timeIntervalSince1970]*1000], type, [NSString stringWithFormat:@"%f", x], [NSString stringWithFormat:@"%f", y], [NSString stringWithFormat:@"%.0f",[myAppDelegate north]], [NSString stringWithFormat:@"%.1f",[myAppDelegate course]], [NSString stringWithFormat:@"%.2f",distance], [NSString stringWithFormat:@"%.6f",location.coordinate.latitude],[NSString stringWithFormat:@"%.6f",location.coordinate.longitude], [NSString stringWithFormat:@"%.2f",curSpeed], nil];
    NSDictionary *entries = [NSDictionary dictionaryWithObjects: objs forKeys:keys];
    [dataArray addObject:entries];
        
    NSInteger countInArray = dataArray.count;
        
    if (countInArray > maxEntries){ 
        
        toWrite = dataArray;
        dataArray = [[NSMutableArray alloc] init];
        //создаем новый тред
        NSThread* myThread = [[NSThread alloc] initWithTarget:databaseAction
                                                         selector:@selector(addArray:)
                                                           object:toWrite];
        [myThread start]; 
    }
    NSLog(@"countInArray = %i", countInArray);
}

- (void)endOfRecord{
    NSLog(@"don't write");
   // dataArray = [[NSMutableArray alloc] init];
    //создаем новый тред
   // NSThread* myThread = [[NSThread alloc] initWithTarget:databaseAction
   //                                              selector:@selector(addArray:)
   //                                                object:toWrite];
   // [myThread start]; 

}

- (void)checkWriteRight{
    if ([myAppDelegate canWriteToFile]) [self addRecord];
    else [self endOfRecord];
}

- (void)sendFile{
    [databaseAction readDatabase]; 
}







@end
