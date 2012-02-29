//
//  DatabaseActions.m
//  SkiBoard
//
//  Created by Елена on 05.01.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "DatabaseActions.h"
#import "AppDelegate.h"

#define myAppDelegate (AppDelegate*) [[UIApplication sharedApplication] delegate]

static sqlite3 *database = nil;
static sqlite3_stmt *deleteStmt = nil;
static sqlite3_stmt *addStmt = nil;



@implementation DatabaseActions

-(id) initDataBase{
	databaseName = @"log.sqlite";
	NSArray *documentPaths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
	NSString *documentsDir = [documentPaths objectAtIndex:0];
	databasePath = [documentsDir stringByAppendingPathComponent:databaseName];
    //[self checkAndCreateDatabase];
    //if (sqlite3_open([databasePath UTF8String], &database) == SQLITE_OK) NSLog(@"Database open");
    //else NSLog(@"error! base not open");
    return self;
}


-(void) checkAndCreateDatabase{
	// Check if the SQL database has already been saved to the users phone, if not then copy it over
	BOOL success;
	success=NO;
	// Create a FileManager object, we will use this to check the status
	// of the database and to copy it over if required
	NSFileManager *fileManager = [NSFileManager defaultManager];
	// Check if the database has already been created in the users filesystem
	success = [fileManager fileExistsAtPath:databasePath];
	
	// If the database already exists then return without doing anything
    if (success) {
        NSLog(@"Data base already exist");
    }
	if(success) return;
	
	// If not then proceed to copy the database from the application to the users filesystem
	
	// Get the path to the database in the application package
	NSString *databasePathFromApp = [[[NSBundle mainBundle] resourcePath] stringByAppendingPathComponent:databaseName];
	
	// Copy the database from the package to the users filesystem
	[fileManager copyItemAtPath:databasePathFromApp toPath:databasePath error:nil];
}

-(void) addRecord: (CMAcceleration) point Type:(int)type{

    CLLocation* location=[myAppDelegate lastLoc];
    
   	if(addStmt == nil) {
		const char *sql = "INSERT INTO log(time, accX, accY, accZ, lon, lat, course, speed, type) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
		if(sqlite3_prepare_v2(database, sql, -1, &addStmt, NULL) != SQLITE_OK)
			NSAssert1(0, @"Error while creating add statement. '%s'", sqlite3_errmsg(database));
	}
    sqlite3_bind_double(addStmt, 1, [[[NSDate alloc ]init]timeIntervalSince1970]);
    sqlite3_bind_double(addStmt, 2, point.x);
    sqlite3_bind_double(addStmt, 3, point.y);
    sqlite3_bind_double(addStmt, 4, point.z);
    sqlite3_bind_double(addStmt, 5, location.coordinate.longitude);
    sqlite3_bind_double(addStmt, 6, location.coordinate.latitude);
    sqlite3_bind_double(addStmt, 7, location.course);
    sqlite3_bind_double(addStmt, 8, location.speed);
    sqlite3_bind_double(addStmt, 9, type);
    
    
	if(SQLITE_DONE != sqlite3_step(addStmt))
		NSAssert1(0, @"Error while inserting data. '%s'", sqlite3_errmsg(database));
	else {
		//SQLite provides a method to get the last primary key inserted by using sqlite3_last_insert_rowid
		pk = sqlite3_last_insert_rowid(database);
        NSLog(@"addRecord SpeedChange! №%i",pk);
	}
	//Reset the add statement.
	sqlite3_reset(addStmt); 
}

- (void) clearDatabase{
    const char *sql = "delete from log";
    if(sqlite3_prepare_v2(database, sql, -1, &deleteStmt, NULL) != SQLITE_OK)
        NSAssert1(0, @"Error while creating delete statement. '%s'", sqlite3_errmsg(database));
    
    if (SQLITE_DONE != sqlite3_step(deleteStmt)) 
        NSAssert1(0, @"Error while deleting. '%s'", sqlite3_errmsg(database));

    //thanks to http://stackoverflow.com/questions/1601697/sqlite-reset-primary-key-field
    sql = "delete from sqlite_sequence where name='log'";
    if(sqlite3_prepare_v2(database, sql, -1, &deleteStmt, NULL) != SQLITE_OK)
        NSAssert1(0, @"Error while creating delete statement. '%s'", sqlite3_errmsg(database));
    
    if (SQLITE_DONE != sqlite3_step(deleteStmt)) 
        NSAssert1(0, @"Error while deleting. '%s'", sqlite3_errmsg(database));
    
    sqlite3_reset(deleteStmt);
}

/*
- (double) takeMaxSpeed{
    
    double maxSpeed = 0;
    const char *sql = "SELECT MAX(speed) FROM speedchangelog";
    
    sqlite3_stmt *selectstmt;
    if(sqlite3_prepare_v2(database, sql, -1, &selectstmt, NULL) == SQLITE_OK) {
        if(sqlite3_step(selectstmt) == SQLITE_ROW){
        maxSpeed = sqlite3_column_double(selectstmt, 0);    
        }
        NSLog(@"max = %f", maxSpeed);
    }
    return maxSpeed;
}
*/

/*
-(NSArray*) readDatabase {
    
	// Init the animals Array
	NSMutableArray *points = [[NSMutableArray alloc] init];
    
	// Open the database from the users filessytem
//	if(sqlite3_open([databasePath UTF8String], &database) == SQLITE_OK) {
		// Setup the SQL Statement and compile it for faster access
		const char *sqlStatement = "select * from speedchangelog";
		sqlite3_stmt *compiledStatement;
		if(sqlite3_prepare_v2(database, sqlStatement, -1, &compiledStatement, NULL) == SQLITE_OK) {
			// Loop through the results and add them to the feeds array
			while(sqlite3_step(compiledStatement) == SQLITE_ROW) {
				// Read the data from the result row
				// Create a new animal object with the data from the database
				Info *point = [[Info alloc] initWithUserID: sqlite3_column_double(compiledStatement, 1)
                                                  UserName:[NSString stringWithUTF8String:(char *)sqlite3_column_text(compiledStatement, 2)]
                                                      Time: sqlite3_column_double(compiledStatement, 3)
                                                     Speed: sqlite3_column_double(compiledStatement, 4) 
                                                 Longitude: sqlite3_column_double(compiledStatement, 5) 
                                                  Latitude: sqlite3_column_double(compiledStatement, 6) 
                                                  Altitude: sqlite3_column_double(compiledStatement, 7)
                               ];
                
				// Add the animal object to the animals Array
				[points addObject:point];
			}
		}
		// Release the compiled statement from memory
		sqlite3_finalize(compiledStatement);
        
//	}
//	sqlite3_close(database);
    return points;
    
}

*/



+ (void) finalizeStatements {
	
	if(database) sqlite3_close(database);
	if(deleteStmt) sqlite3_finalize(deleteStmt);
	if(addStmt) sqlite3_finalize(addStmt);
}


@end
