//
//  FileController.h
//  GoodRoads
//
//  Created by Елена on 18.08.11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "ZipArchive.h"





@interface FileController : NSObject {
    
    NSFileManager	*fileMgr;
	NSArray			*paths;
	NSString		*documentsDirectory;
    
    NSInteger       fileCount;
    NSString        *fileSize;
    NSInteger       size;

}

@property (nonatomic, retain) 	NSFileManager	*fileMgr;
@property (nonatomic, retain) 	NSArray			*paths;
@property (nonatomic, retain) 	NSString		*documentsDirectory;
@property (nonatomic, retain) 	NSString		*filePath;
@property (nonatomic)   NSInteger       fileCount;
@property (nonatomic)   NSInteger       size;

- (BOOL) createFile;
- (BOOL) writeToFile:(NSString *)myString fileName: (NSString *)fileName;
- (BOOL) deleteFile;
- (BOOL) isFileEmpty;
- (NSString *) readFile: (NSString *)path;
- (void) countFiles;
- (NSArray *) arrayFiles;
- (NSData *)makeArchive;
- (NSMutableArray *) getAllFiles;
+ (void)write:(NSString *)data;
+ (NSString *)filePath;


@end
