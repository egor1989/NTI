//
//  FileController.m
//  GoodRoads
//
//  Created by Елена on 18.08.11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#import "FileController.h"



@implementation FileController

@synthesize fileMgr, paths, documentsDirectory, filePath;

- (id)init{
    
    self = [super init];
    
    // create the fileManager
	self.fileMgr = [NSFileManager defaultManager];
	
	// get array of paths
	self.paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
	
	// get documents directory path
	self.documentsDirectory = [paths objectAtIndex:0];
    
    // create the file name with qualified path ot the file
	
   
    
	return self;
}

- (BOOL) writeToFile:(NSString *)myString fileName: (NSString *)fileName{
    
    NSLog(@"fileName %@", fileName);
    self.filePath = [documentsDirectory stringByAppendingPathComponent: fileName];
    
    NSLog(@"************* writeToFile *************");
    if(![self.fileMgr fileExistsAtPath:self.filePath]){
        [self.fileMgr createFileAtPath:filePath contents:nil attributes:nil];
        NSLog(@"file created");
       
    }
    NSFileHandle *fileHandle = [NSFileHandle fileHandleForWritingAtPath:filePath];
    [fileHandle seekToEndOfFile];
    NSData* myData = [myString dataUsingEncoding:NSUTF8StringEncoding];
    [fileHandle writeData:myData];

    return TRUE;
}

- (BOOL) createFile{
    NSLog(@"********** create file *********");
    
    // check to see if file already exists in documents directory
    if([self.fileMgr fileExistsAtPath:self.filePath])
        NSLog(@"File already exists");
    //file does not exist
    else {
        [self.fileMgr createFileAtPath:filePath contents:nil attributes:nil];
    }
        
    return TRUE;
    
}

- (BOOL) deleteFile{
    
	NSLog(@"************* deleteFileAction *************");
	
	//Test whether this file exists now that we have tried to remove it
	if([self.fileMgr fileExistsAtPath:self.filePath])
	{
		NSLog(@"File exists try removing it");
		// attempt to delete file from documents directory
		[fileMgr removeItemAtPath:self.filePath error:nil];
        
		if([self.fileMgr fileExistsAtPath:self.filePath])
		{
			NSLog(@"File still exists after trying to remove it");
            return FALSE;
		}
		else 
		{
			NSLog(@"We've successfully removed the file");
            return TRUE;
		}
	}
	else {
		NSLog(@"File does not exist no need to remove it.");
        return FALSE;
		
	}
    
    
}

- (NSString *) readFile{
    
    NSLog(@"************* readFileAction *************");
	
	//Test whether this file exists before we read it
	if([self.fileMgr fileExistsAtPath:self.filePath])
	{
		NSLog(@"File exists we can now try to read it");
        NSString *content = [[NSString alloc] initWithContentsOfFile:self.filePath usedEncoding:nil error:nil];
        return content;
	}
	else 
	{
		NSLog(@"File does not exist no need trying to read it");
        return NULL;
	}
}




- (BOOL) isFileEmpty {
    if([self.fileMgr fileExistsAtPath:self.filePath]){
        NSLog(@"************* Check isFileIsEmpty *************");
        NSString *content = [[NSString alloc] initWithContentsOfFile:self.filePath usedEncoding:nil error:nil];
        return [content isEqualToString:@""];
    }
    else{
        return TRUE;
    }
    
}



@end
