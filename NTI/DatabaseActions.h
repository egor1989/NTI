//
//  DatabaseActions.h
//  SkiBoard
//
//  Created by Елена on 05.01.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreLocation/CoreLocation.h>
#import <CoreMotion/CoreMotion.h>
#import <sqlite3.h> 
#import "toJSON.h"
#import "ServerCommunication.h"
#import "CSVConverter.h"
#import "GzipCompress.h"

@interface DatabaseActions : NSObject{
    // Database variables
	NSString *databaseName;
	NSString *databasePath;
    CLLocation *userLocation;
    
    //data for table
    NSInteger pk;
    double time;
    double speedDiff, currentAcceleration;
    double timeOver, maxOver;
   // NSMutableDictionary *record;
   // NSMutableDictionary *acc;
   // NSMutableDictionary *gps;
    NSMutableArray *dataArray;
    NSUserDefaults *userDefaults;
    CSVConverter *csvConverter;
    ServerCommunication *serverCommunication;
    toJSON *jsonConvert;


    
}

- (id) initDataBase;
- (void) checkAndCreateDatabase;
+ (void) clearDatabase;
- (BOOL) addArray: (NSMutableArray *)data;
- (void) sendDatabase;
+ (void) finalizeStatements;
- (void) convertAndSend;
- (BOOL) addEntrie: (NSString *)type;
- (void) sendDatabaseTr;
+ (BOOL) needLastRoute;
+ (void)setNeedLastRoute: (BOOL) isNeed;




@end
