//
//  recordAction.h
//  NTI
//
//  Created by Елена on 31.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "DatabaseActions.h"
#import "FileController.h"
#import "toJSON.h"
#import "CSVConverter.h"
#import "AppDelegate.h"


#define myAppDelegate (AppDelegate*) [[UIApplication sharedApplication] delegate]

@interface recordAction : NSObject{
    DatabaseActions *databaseAction;

    FileController *fileController;   
    NSUserDefaults *userDefaults;
    NSArray *keys;
    NSMutableArray *dataArray;
    int k;
    CSVConverter *csvConverter;
    toJSON *jsonConvert;
    CLLocation *location;
    
    CMAcceleration userAcceleration;
    CMAcceleration gravity;
    int maxGravityAxe;
    double x,y;
    NSString *type;
    BOOL writeToDB;
    
}



-(id)initRecording;
//-(void) accelerometerReciver: (NSNotification*) theNotice;
-(void)recording;



@end
