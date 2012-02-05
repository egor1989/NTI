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
    
}

- (id) initDataBase;
- (void) checkAndCreateDatabase;
- (void) addRecord: (CMAcceleration) point;
- (void) clearDatabase;
//- (NSArray*) readDatabase;
+ (void) finalizeStatements;
@end
