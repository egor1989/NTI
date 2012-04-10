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
    userDefaults = [NSUserDefaults standardUserDefaults];
    jsonConvert = [[toJSON alloc]init];
    fileController = [[FileController alloc] init];
    csvConverter = [[CSVConverter alloc] init];
    serverCommunication = [[ServerCommunication alloc] init];
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
    
    //NSLog(@"data=%@", data);
    
        const char *sql = "INSERT INTO log(type, time, accX, accY, compass, direction, distance, latitude, longitude, speed) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if(addStmt == nil) {
        if(sqlite3_prepare_v2(database, sql, -1, &addStmt, NULL) != SQLITE_OK){
            NSAssert1(0, @"Error while creating add statement. '%s'", sqlite3_errmsg(database));
            return NO;
        }
    }
    sqlite3_exec(database, "BEGIN", NULL, NULL, NULL);
    for (NSDictionary *entrie in data) {

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
            //sqlite3_
            pk = sqlite3_last_insert_rowid(database);
            NSLog(@"addRecord %i",pk);
            [userDefaults setInteger:pk forKey:@"pk"];
            
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
    
    //если придется вернуть очистку рк
    //thanks to http://stackoverflow.com/questions/1601697/sqlite-reset-primary-key-field
}

- (void) readDatabase{  
    NSArray *keys = [NSArray arrayWithObjects:@"timestamp", @"type", @"acc", @"gps", nil];
    dataArray = [[NSMutableArray alloc]init];
    
    
    if(sqlite3_open([databasePath UTF8String], &database) == SQLITE_OK) {

       // NSLog(@"%i", [userDefaults integerForKey:@"pk"]);
        
        for (NSInteger i=1;i<=([userDefaults integerForKey:@"pk"]/maxEntries)+1;i++) {

            const char * sql = [[NSString stringWithFormat:@"SELECT * FROM log WHERE rowid BETWEEN %i AND %i", (i-1)*maxEntries, i*maxEntries] UTF8String];
           
            NSLog(@"%s", sql);
            
            if(sqlite3_prepare_v2(database, sql, -1, &readStmt, NULL) == SQLITE_OK){
                
                while(sqlite3_step(readStmt) == SQLITE_ROW){
                    
                    NSDictionary *acc = [NSDictionary dictionaryWithObjectsAndKeys:[NSString stringWithFormat:@"%f", sqlite3_column_double(readStmt, 3)], @"x", [NSString stringWithFormat:@"%f", sqlite3_column_double(readStmt, 4)], @"y", nil];
                    
                    NSDictionary *gps = [NSDictionary dictionaryWithObjectsAndKeys:[NSString stringWithFormat:@"%.1f", sqlite3_column_double(readStmt, 6)], @"direction", [NSString stringWithFormat:@"%.1f", sqlite3_column_double(readStmt, 10)], @"speed", [NSString stringWithFormat:@"%f", sqlite3_column_double(readStmt, 8)], @"latitude", [NSString stringWithFormat:@"%f", sqlite3_column_double(readStmt, 9)], @"longitude", [NSString stringWithFormat:@"%.0f", sqlite3_column_double(readStmt, 5)], @"compass", [NSString stringWithFormat:@"%.2f", sqlite3_column_double(readStmt, 7)], @"distance", nil];
                    
                    NSArray *objs = [NSArray arrayWithObjects:  [NSString stringWithFormat:@"%.0f", sqlite3_column_double(readStmt, 2)],[NSString stringWithFormat:@"%s", sqlite3_column_text(readStmt, 1)], 
                                     acc, gps, nil];
                    
                    NSDictionary *record = [NSDictionary dictionaryWithObjects:objs forKeys:keys];
                    
                    
                    //  record = [[NSMutableDictionary alloc] init];
                    //  [record setObject: [NSString stringWithFormat:@"%s", sqlite3_column_text(readStmt, 0)] forKey:@"type"];
                    //  [record setObject: [NSString stringWithFormat:@"%.0f", sqlite3_column_double(readStmt, 1)] forKey:@"time"];
                    //  [record setObject: [NSString stringWithFormat:@"%f", sqlite3_column_double(readStmt, 2)] forKey:@"accX"];
                    //  [record setObject: [NSString stringWithFormat:@"%f", sqlite3_column_double(readStmt, 3)] forKey:@"accY"];
                    //  [record setObject: [NSString stringWithFormat:@"%.0f", sqlite3_column_double(readStmt, 4)] forKey:@"compass"];
                    //  [record setObject: [NSString stringWithFormat:@"%.1f", sqlite3_column_double(readStmt, 5)] forKey:@"direction"];
                    //  [record setObject: [NSString stringWithFormat:@"%.2f", sqlite3_column_double(readStmt, 6)] forKey:@"distance"];
                    // [record setObject: [NSString stringWithFormat:@"%f", sqlite3_column_double(readStmt, 7)] forKey:@"latitude"];
                    //   [record setObject: [NSString stringWithFormat:@"%f", sqlite3_column_double(readStmt, 8)] forKey:@"longitude"];
                    // [record setObject: [NSString stringWithFormat:@"%.1f", sqlite3_column_double(readStmt, 9)] forKey:@"speed"];
                    
                    [dataArray addObject:record];
                    

                }
            } else NSLog(@"indalid command");
            if ([self convertAndWrite]) dataArray = [[NSMutableArray alloc]init];
              
        }
     }
    
    sqlite3_finalize(readStmt);
    [self clearDatabase];
}

- (BOOL) convertAndWrite{
    NSInteger size = [dataArray count];
    NSLog(@"%i",size);
    NSString *CSV = [csvConverter arrayToCSVString:dataArray];
    NSString *JSON = [jsonConvert convert:dataArray];
    
    NSDateFormatter * date_format = [[NSDateFormatter alloc] init];
    [date_format setDateFormat: @"dd.MM.YYYY"]; 
    NSLog (@"Date: %@", [date_format stringFromDate:[NSDate date]]);
     
    
    if ([serverCommunication checkInternetConnection]) {
        NSLog(@"стефу");
        [serverCommunication uploadData: JSON]; 
    }
    else {
    [fileController writeToFile:JSON fileName:[[date_format stringFromDate:[NSDate date]] stringByAppendingString:@".json"]];
        NSLog(@"интернета нет - записано в локальный файл");
    }
    
    if ([fileController writeToFile:CSV fileName: [date_format stringFromDate:[NSDate date]]]) return YES;
    else return NO;
}

- (BOOL) deleteRowsFrom: (NSInteger)start To: (NSInteger)end{
    
    NSLog(@"from %i to %i", start, end);
    const char *sql = [[NSString stringWithFormat:@"DELETE FROM log WHERE rowid BETWEEN %i AND %i", start, end] UTF8String];
    if(sqlite3_prepare_v2(database, sql, -1, &deleteStmt, NULL) != SQLITE_OK){
        NSAssert1(0, @"Error while creating delete statement. '%s'", sqlite3_errmsg(database));
        return NO;
    }
    
    if (SQLITE_DONE != sqlite3_step(deleteStmt)) {
        NSAssert1(0, @"Error while deleting. '%s'", sqlite3_errmsg(database));
        return NO;
    }
    
    sqlite3_reset(deleteStmt); 
    return YES;
}

+ (void) finalizeStatements {
	
	if(database) sqlite3_close(database);
	if(deleteStmt) sqlite3_finalize(deleteStmt);
	if(addStmt) sqlite3_finalize(addStmt);
}


@end
