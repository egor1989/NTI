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

}

@property (nonatomic, retain) 	NSFileManager	*fileMgr;
@property (nonatomic, retain) 	NSArray			*paths;
@property (nonatomic, retain) 	NSString		*documentsDirectory;
@property (nonatomic, retain) 	NSString		*filePath;

- (BOOL) createFile;
- (BOOL) writeToFile:(NSString *)myString fileName: (NSString *)fileName;
- (BOOL) deleteFile;
- (BOOL) isFileEmpty;
- (NSString*) readFile;
- (NSString *)getAttachment: (NSInteger) i;
- (NSInteger) countFiles;
- (NSArray *) arrayFiles;
- (BOOL)makeArchive;



@end
