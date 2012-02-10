//
//  FileController.h
//  GoodRoads
//
//  Created by Елена on 18.08.11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>




@interface FileController : NSObject {
    
    NSFileManager	*fileMgr;
	NSArray			*paths;
	NSString		*documentsDirectory;
    NSString        *fileName;
}

@property (nonatomic, retain) 	NSFileManager	*fileMgr;
@property (nonatomic, retain) 	NSArray			*paths;
@property (nonatomic, retain) 	NSString		*documentsDirectory;
@property (nonatomic, retain) 	NSString		*filePath;

- (id) init;
- (BOOL) createFile;
- (BOOL) writeToFile:(NSString *)myString;
- (BOOL) deleteFile;
- (BOOL) isFileEmpty;
- (NSString*) readFile;
- (NSString*) getFileName;


@end
