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
#import "FileController.h"
#import "toJSON.h"
#import "ServerCommunication.h"
#import "CSVConverter.h"


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
    FileController *fileController;
    
    

    
}

- (id) initDataBase;
- (void) checkAndCreateDatabase;
- (void) clearDatabase;
- (BOOL)addArray: (NSMutableArray *)data;
- (void) readDatabase;
+ (void) finalizeStatements;
- (BOOL) convertAndWrite;
- (BOOL) deleteRowsFrom: (NSInteger)start To: (NSInteger)end;
- (BOOL) addEntrie: (NSString *)type;
@end
