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
#define maxEntries 1000

static sqlite3 *database = nil;
static sqlite3_stmt *deleteStmt = nil;
static sqlite3_stmt *addStmt = nil;
static sqlite3_stmt *readStmt = nil;




@implementation DatabaseActions

-(id) initDataBase{
	databaseName = @"log.sqlite";
	NSArray *documentPaths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
	NSString *documentsDir = [documentPaths objectAtIndex:0];
	databasePath = [documentsDir stringByAppendingPathComponent:databaseName];
    [self checkAndCreateDatabase];
    if (sqlite3_open([databasePath UTF8String], &database) == SQLITE_OK) NSLog(@"Database open");
    else NSLog(@"error! base not open");
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

- (BOOL)addArray: (NSMutableArray *)data{
    
    NSLog(@"data=%@", data);
    
        const char *sql = "INSERT INTO log(type, time, accX, accY, compass, direction, distance, latitude, longitude, speed) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if(addStmt == nil) {
        if(sqlite3_prepare_v2(database, sql, -1, &addStmt, NULL) != SQLITE_OK){
            NSAssert1(0, @"Error while creating add statement. '%s'", sqlite3_errmsg(database));
            return NO;
        }
    }
    sqlite3_exec(database, "BEGIN", NULL, NULL, NULL);
    for (NSDictionary *entrie in data) {
        //sqlite3_exec(databaseName, "BEGIN", 0, 0, 0);

        sqlite3_bind_text(addStmt, 1, [[entrie objectForKey:@"type"] UTF8String], -1, SQLITE_TRANSIENT);
        sqlite3_bind_double(addStmt, 2, [[entrie objectForKey:@"timestamp"] doubleValue]);
        sqlite3_bind_double(addStmt, 3, [[entrie objectForKey:@"accX"] doubleValue]);
        sqlite3_bind_double(addStmt, 4, [[entrie objectForKey:@"accY"] doubleValue]);
        sqlite3_bind_double(addStmt, 5, [[entrie objectForKey:@"compass"] doubleValue]);
        sqlite3_bind_double(addStmt, 6, [[entrie objectForKey:@"direction"] doubleValue]);
        sqlite3_bind_double(addStmt, 7, [[entrie objectForKey:@"distance"] doubleValue]);
        sqlite3_bind_double(addStmt, 8, [[entrie objectForKey:@"latitude"] doubleValue]);
        sqlite3_bind_double(addStmt, 9, [[entrie objectForKey:@"longitude"] doubleValue]);
        sqlite3_bind_double(addStmt, 10, [[entrie objectForKey:@"speed"] doubleValue]);
        
        
        if(SQLITE_DONE != sqlite3_step(addStmt)){
            NSAssert1(0, @"Error while inserting data. '%s'", sqlite3_errmsg(database));
            return NO;
        }
        else {
            //SQLite provides a method to get the last primary key inserted by using sqlite3_last_insert_rowid
            pk = sqlite3_last_insert_rowid(database);
            NSLog(@"addRecord  №%i",pk);
            
        }
        
        
        //Reset the add statement.
        sqlite3_reset(addStmt); 
        sqlite3_clear_bindings(addStmt);
        
        
    }
    sqlite3_exec(database, "COMMIT", NULL, NULL, NULL);
    
    return YES;
}


- (void) clearDatabase{
    const char *sql = "delete from log";
    if(sqlite3_prepare_v2(database, sql, -1, &deleteStmt, NULL) != SQLITE_OK)
        NSAssert1(0, @"Error while creating delete statement. '%s'", sqlite3_errmsg(database));
    
    if (SQLITE_DONE != sqlite3_step(deleteStmt)) 
        NSAssert1(0, @"Error while deleting. '%s'", sqlite3_errmsg(database));
    
    sqlite3_reset(deleteStmt); 
    sqlite3_clear_bindings(deleteStmt);

    //thanks to http://stackoverflow.com/questions/1601697/sqlite-reset-primary-key-field
    sql = "delete from sqlite_sequence where name='log'";
    if(sqlite3_prepare_v2(database, sql, -1, &deleteStmt, NULL) != SQLITE_OK)
        NSAssert1(0, @"Error while creating delete statement. '%s'", sqlite3_errmsg(database));
    
    if (SQLITE_DONE != sqlite3_step(deleteStmt)) 
        NSAssert1(0, @"Error while deleting. '%s'", sqlite3_errmsg(database));
    NSLog(@"dataBaseClear");
    sqlite3_reset(deleteStmt);
    
}

- (void) readDatabase{
   /*
    NSArray *objs = [NSArray arrayWithObjects:  [NSString stringWithFormat:@"%.0f",[[[NSDate alloc ]init]timeIntervalSince1970]*1000], type, [NSString stringWithFormat:@"%f", x], [NSString stringWithFormat:@"%f", y], [NSString stringWithFormat:@"%.0f",[myAppDelegate north]], [NSString stringWithFormat:@"%.1f",[myAppDelegate course]], [NSString stringWithFormat:@"%.2f",distance], [NSString stringWithFormat:@"%.6f",location.coordinate.latitude],[NSString stringWithFormat:@"%.6f",location.coordinate.longitude], [NSString stringWithFormat:@"%.2f",curSpeed], nil];
    NSDictionary *entries = [NSDictionary dictionaryWithObjects: objs forKeys:keys];
    [dataArray addObject:entries];
    */
    
    
    NSMutableArray *dataArray = [[NSMutableArray alloc]init ];
    if(sqlite3_open([databasePath UTF8String], &database) == SQLITE_OK) {
        const char *sql = "select * from log";
        if(sqlite3_prepare_v2(database, sql, -1, &readStmt, NULL) == SQLITE_OK){
            while(sqlite3_step(readStmt) == SQLITE_ROW){
                record = [[NSMutableDictionary alloc] init];
                [record setObject:[NSString stringWithFormat:@"%s", sqlite3_column_text(readStmt, 1)] forKey:@"type"];
                [record setValue: [NSString stringWithFormat:@"%f", sqlite3_column_text(readStmt, 2)] forKey:@"time"];
                [record setValue: [NSString stringWithFormat:@"%f", sqlite3_column_text(readStmt, 3)] forKey:@"accX"];
                [record setValue: [NSString stringWithFormat:@"%f", sqlite3_column_text(readStmt, 4)] forKey:@"accY"];
                [record setValue: [NSString stringWithFormat:@"%f", sqlite3_column_text(readStmt, 5)] forKey:@"compass"];
                [record setValue: [NSString stringWithFormat:@"%f", sqlite3_column_text(readStmt, 6)] forKey:@"direction"];
                [record setValue: [NSString stringWithFormat:@"%f", sqlite3_column_text(readStmt, 7)] forKey:@"distance"];
                [record setValue: [NSString stringWithFormat:@"%f", sqlite3_column_text(readStmt, 8)] forKey:@"latitude"];
                [record setValue: [NSString stringWithFormat:@"%f", sqlite3_column_text(readStmt, 9)] forKey:@"longitude"];
                [record setValue: [NSString stringWithFormat:@"%f", sqlite3_column_text(readStmt, 10)] forKey:@"speed"];
            }
            [dataArray addObject:record];
        }
    }
    NSLog(@"+");
     NSLog(@"data = %@", dataArray);
    
}




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
