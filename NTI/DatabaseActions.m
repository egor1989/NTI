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
#define maxEntries 500

static sqlite3 *database = nil;
static sqlite3_stmt *deleteStmt = nil;
static sqlite3_stmt *addStmt = nil;
static sqlite3_stmt *addEntr = nil;
static sqlite3_stmt *readStmt = nil;
static BOOL needLastRoute;



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
    needLastRoute = NO;
    csvConverter = [[CSVConverter alloc] init];
    serverCommunication = [[ServerCommunication alloc] init];
    return self;
}

+ (BOOL)needLastRoute
{
    return needLastRoute;
}

+ (void)setNeedLastRoute: (BOOL) isNeed
{
    needLastRoute = isNeed;
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
    NSLog(@"DataBase add array");
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
            NSLog(@"database error (insert) = %@", sqlite3_errmsg(database));
            NSAssert1(0, @"Error while inserting data. '%s'", sqlite3_errmsg(database));
            return NO;
        }
        else {
            //SQLite provides a method to get the last primary key inserted by using sqlite3_last_insert_rowid
            //sqlite3_
            pk = sqlite3_last_insert_rowid(database);
//            NSLog(@"addRecord %i",pk);
            [userDefaults setInteger:pk forKey:@"pk"];
            NSLog(@"add entrie%i",pk);
            
        }
        
        
        //Reset the add statement.
        sqlite3_reset(addStmt); 
        sqlite3_clear_bindings(addStmt);
        
        
    }
    sqlite3_exec(database, "COMMIT", NULL, NULL, NULL);
    
    return YES;
}

- (BOOL)addEntrie: (NSString *)type{
    
    NSLog(@"addEntrie");
   // double currentTime = [[NSData data] timeIntervalSince1970];
    NSLog(@" time = %f, type = %@",[[NSDate date] timeIntervalSince1970], type);
    
    const char *sql = "INSERT INTO log(type, time, accX, accY, compass, direction, distance, latitude, longitude, speed) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if(addEntr == nil) {
        if(sqlite3_prepare_v2(database, sql, -1, &addEntr, NULL) != SQLITE_OK){
            NSAssert1(0, @"Error while creating add statement. '%s'", sqlite3_errmsg(database));
            NSLog( @"database error");
            return NO;
        }
    }
    sqlite3_bind_text(addEntr, 1, [type UTF8String], -1, SQLITE_TRANSIENT);
    sqlite3_bind_double(addEntr, 2, [[NSDate date] timeIntervalSince1970]);
    
    //
    //
    //
    
    if(SQLITE_DONE != sqlite3_step(addEntr)){
        NSLog(@"database error");
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
    sqlite3_reset(addEntr); 
    sqlite3_clear_bindings(addEntr);

    
    return YES;

}




+ (void) clearDatabase{
    NSLog(@"clear DB");
    const char *sql = "delete from log";
    if(sqlite3_prepare_v2(database, sql, -1, &deleteStmt, NULL) != SQLITE_OK){
        NSLog(@"error while creating delete statement %@", sqlite3_errmsg(database));
        NSAssert1(0, @"Error while creating delete statement. '%s'", sqlite3_errmsg(database));
    }
    
    if (SQLITE_DONE != sqlite3_step(deleteStmt)){ 
        NSLog(@"error while deleting %@", sqlite3_errmsg(database));
        NSAssert1(0, @"Error while deleting. '%s'", sqlite3_errmsg(database));
    }
    sqlite3_reset(deleteStmt); 
    
    
    //если придется вернуть очистку рк
    //thanks to http://stackoverflow.com/questions/1601697/sqlite-reset-primary-key-field
}

- (void) sendDatabase{
    NSLog(@"send DB");
    NSThread* myThread = [[NSThread alloc] initWithTarget:self
                                        selector:@selector(sendDatabaseTr)
                                        object:nil];
    
    [myThread start]; 
                          

}



- (void) sendDatabaseTr{
    BOOL noerror = YES;
    NSLog(@"sendDB thread");
    NSArray *keys = [NSArray arrayWithObjects:@"timestamp", @"type", @"acc", @"gps", nil];
    dataArray = [[NSMutableArray alloc]init];
    
    if(sqlite3_open([databasePath UTF8String], &database) == SQLITE_OK) {
        
        for (NSInteger i=1;i<=([userDefaults integerForKey:@"pk"]/maxEntries)+1;i++) {
            NSLog(@"SELECT * FROM log WHERE rowid BETWEEN %i AND %i\n", (i-1)*maxEntries, i*maxEntries);
            const char * sql = [[NSString stringWithFormat:@"SELECT * FROM log WHERE rowid BETWEEN %i AND %i", (i-1)*maxEntries, i*maxEntries] UTF8String];

            if(sqlite3_prepare_v2(database, sql, -1, &readStmt, NULL) == SQLITE_OK){
                
                while(sqlite3_step(readStmt) == SQLITE_ROW){
                    
                    NSDictionary *acc = [NSDictionary dictionaryWithObjectsAndKeys:[NSString stringWithFormat:@"%f", sqlite3_column_double(readStmt, 3)], @"x", [NSString stringWithFormat:@"%f", sqlite3_column_double(readStmt, 4)], @"y", nil];
                    
                    NSDictionary *gps = [NSDictionary dictionaryWithObjectsAndKeys:[NSString stringWithFormat:@"%.1f", sqlite3_column_double(readStmt, 6)], @"direction", [NSString stringWithFormat:@"%.1f", sqlite3_column_double(readStmt, 10)], @"speed", [NSString stringWithFormat:@"%f", sqlite3_column_double(readStmt, 8)], @"latitude", [NSString stringWithFormat:@"%f", sqlite3_column_double(readStmt, 9)], @"longitude", [NSString stringWithFormat:@"%.0f", sqlite3_column_double(readStmt, 5)], @"compass", [NSString stringWithFormat:@"%.2f", sqlite3_column_double(readStmt, 7)], @"distance", nil];
                    
                    NSArray *objs = [NSArray arrayWithObjects:  [NSString stringWithFormat:@"%.0f", sqlite3_column_double(readStmt, 2)],[NSString stringWithFormat:@"%s", sqlite3_column_text(readStmt, 1)], 
                                     acc, gps, nil];
                    
                    NSDictionary *record = [NSDictionary dictionaryWithObjects:objs forKeys:keys];

                    [dataArray addObject:record];
                    
                    
                }
            }
            else 
                {
                    noerror = NO;
                    NSLog(@"indalid command");
                }
            
            if (noerror) {
                [self convertAndSend];
                dataArray = [[NSMutableArray alloc]init];
            }
            
        }
    }
    if (noerror) {
        sqlite3_finalize(readStmt);
        [serverCommunication showResult];
            if (![serverCommunication errors]){
                NSLog(@"DBsend - no errors");
                [DatabaseActions clearDatabase];
                needLastRoute = YES;
                [userDefaults setValue: [serverCommunication getLastStatistic] forKey:@"lastStat"];
                [userDefaults setValue: [serverCommunication getAllStatistic] forKey:@"allStat"];
                //notif refresh
            }
    }
    noerror = YES;
    sqlite3_close(database);
    [myAppDelegate startRecord];
}

- (void) convertAndSend{
    NSLog(@"data array size = %i",[dataArray count]);

  //  NSString *CSV = [csvConverter arrayToCSVString:dataArray];
    NSData *JSON = [jsonConvert convert:dataArray];
   
  //  NSString *cJSON = [[NSString alloc] initWithData:JSON encoding:NSASCIIStringEncoding]; 
    //NSLog(@"cJSON=%@",cJSON);
  // для шифрования  
    [serverCommunication uploadData: JSON]; 
    
  // без шифрования [serverCommunication uploadData: cJSON]; 

}


+ (void) finalizeStatements {
    NSLog(@"finalizeStatements");
	if(database) sqlite3_close(database);
	if(deleteStmt) sqlite3_finalize(deleteStmt);
	if(addStmt) sqlite3_finalize(addStmt);
}


@end
