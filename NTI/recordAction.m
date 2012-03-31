//
//  recordAction.m
//  NTI
//
//  Created by Елена on 31.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "recordAction.h"
#import "StatViewController.h"

@implementation recordAction


#define MAX3(a,b,c) ( MAX(a,b)>c ? ((a>b)? 1:2) : 3 )
#define radianConst M_PI/180.0
#define maxEntries 100

-(id)initRecording{
    
    
    k=0;
    databaseAction = [[DatabaseActions alloc] initDataBase];
    writeToDB = NO;
    userDefaults = [NSUserDefaults standardUserDefaults];
    
    keys = [NSArray arrayWithObjects:@"timestamp", @"type", @"accX", @"accY", @"compass", @"direction", @"distance", @"latitude", @"longitude",@"speed", nil];
    
    jsonConvert = [[toJSON alloc]init];
    fileController = [[FileController alloc] init];
    csvConverter = [[CSVConverter alloc] init];
    type = @"-";
    
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(accelerometer:)
     name: @"motionNotification"
     object: nil];
    
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(recording)
     name: @"recordAction"
     object: nil];
    


    return self;
}

- (void) accelerometer: (NSNotification*) theNotice{
    userAcceleration=((CMDeviceMotion*)[theNotice.userInfo objectForKey: @"motion"]).userAcceleration;
    gravity=((CMDeviceMotion*)[theNotice.userInfo objectForKey: @"motion"]).gravity;
    maxGravityAxe = MAX3(fabs(gravity.x), fabs(gravity.y), fabs(gravity.z));
    if (maxGravityAxe==1){
        x=-userAcceleration.z;
        y=userAcceleration.y;
    }
    else{
        if (maxGravityAxe==2){
            x=-userAcceleration.x;
            y=-userAcceleration.z;
        }
        else{
            if (maxGravityAxe==3){
                x=userAcceleration.x;//!!
                y=userAcceleration.y;
            }
        }
    }

    
    if (writeToDB) {
        float curSpeed = 0;
        if (location.speed > 0) curSpeed = location.speed*3.6;
        
        float distance = [myAppDelegate allDistance]/1000;
        
        NSArray *objs = [NSArray arrayWithObjects:  [NSString stringWithFormat:@"%.0f",[[[NSDate alloc ]init]timeIntervalSince1970]*1000], type, [NSString stringWithFormat:@"%f", x], [NSString stringWithFormat:@"%f", y], [NSString stringWithFormat:@"%.0f",[myAppDelegate north]], [NSString stringWithFormat:@"%.1f",[myAppDelegate course]], [NSString stringWithFormat:@"%.2f",distance], [NSString stringWithFormat:@"%.6f",location.coordinate.latitude],[NSString stringWithFormat:@"%.6f",location.coordinate.longitude], [NSString stringWithFormat:@"%.2f",curSpeed], nil];
        NSDictionary *entries = [NSDictionary dictionaryWithObjects: objs forKeys:keys];
        [dataArray addObject:entries];
        
        NSInteger countInArray = dataArray.count;
        
        if (countInArray > maxEntries){ 
            //countInArray = 0;
            NSMutableArray *toWrite = dataArray;
            dataArray = [[NSMutableArray alloc] init];
            //создаем новый тред
            NSThread* myThread = [[NSThread alloc] initWithTarget:databaseAction
                                                         selector:@selector(addArray:)
                                                           object:toWrite];
            [myThread start]; 
            
            
        }
        
        NSLog(@"countInArray = %i", countInArray);
    }
  //  if ([myAppDelegate canWriteToFile]) writeLabel.text = @"+";
  //  else writeLabel.text = @"-";
    
}

-(void)recording{
    StatViewController *statistic = [[StatViewController alloc] init];
    if ([statistic writeAction]) writeToDB=YES;
    else writeToDB = NO;
}


@end
