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
	NSError *error = nil;
    for (NSString *file in [self.fileMgr contentsOfDirectoryAtPath:self.documentsDirectory error:&error])
    {    
        NSString *deleteFilePath = [self.documentsDirectory stringByAppendingPathComponent:file];
        NSLog(@"File : %@", deleteFilePath);
        
        BOOL fileDeleted = [self.fileMgr removeItemAtPath:deleteFilePath error:&error];
        
        if (fileDeleted != YES || error != nil)
        {
            NSLog(@"ERROR!");
        }
    }
    
    NSUserDefaults *userDefaults =[NSUserDefaults standardUserDefaults];
    [userDefaults setInteger:0 forKey: @"accelFileNumber"];
    [userDefaults setInteger:0 forKey: @"decelFileNumber"];
    [userDefaults setInteger:0 forKey: @"leftRotFileNumber"];
    [userDefaults setInteger:0 forKey: @"rightRotFileNumber"];
    [userDefaults setInteger:0 forKey: @"otherFile"];
    [userDefaults synchronize];
    
    return YES;
    
    
    
	//Test whether this file exists now that we have tried to remove it
/*	if([self.fileMgr fileExistsAtPath:self.filePath])
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
  */  
    
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

- (NSString *)getAttachment: (NSInteger) i{

    NSError *error = nil;
    NSString *file = [[self.fileMgr contentsOfDirectoryAtPath:self.documentsDirectory error:&error] objectAtIndex:i];
    NSString *sendFilePath = [self.documentsDirectory stringByAppendingPathComponent:file];
    NSLog(@"File : %@", sendFilePath);
    
    NSString *attachment = [NSString stringWithContentsOfFile:sendFilePath encoding:NSUTF8StringEncoding error:&error];
    [self makeArchive];
    return attachment;
}

- (NSInteger) countFiles{
    NSError *error = nil;
    NSInteger count = [[self.fileMgr contentsOfDirectoryAtPath:self.documentsDirectory error:&error] count];
    NSLog(@"countFile %i", count);
    return count;
}

- (NSArray *) arrayFiles{
    NSError *error = nil;
    NSArray *arrayFiles = [self.fileMgr contentsOfDirectoryAtPath:self.documentsDirectory error:&error];
    return arrayFiles;
                           
}

-(BOOL) makeArchive {
   // BOOL isDir=NO;	
    NSError *error = nil;
    NSArray *subpaths = [self.fileMgr contentsOfDirectoryAtPath:self.documentsDirectory error:&error];	
   // NSString *exportPath = @"exportData";
    //NSFileManager *fileManager = [NSFileManager defaultManager];	
    //if ([fileManager fileExistsAtPath:exportPath isDirectory:&isDir] && isDir){
    //    subpaths = [fileManager subpathsAtPath:exportPath];
   // }
    
  //  NSLog(@"fileName %@", fileName);
  //  self.filePath = [documentsDirectory stringByAppendingPathComponent: fileName];

    
    NSString *archivePath = [documentsDirectory stringByAppendingPathComponent: @"exportData.zip"];//@"exportData.zip";
    
    ZipArchive *archiver = [[ZipArchive alloc] init];
    [archiver CreateZipFile2:archivePath];
    for(NSString *path in subpaths){		
        // Only add it if it's not a directory. ZipArchive will take care of those.
        //NSString *deleteFilePath = [self.documentsDirectory stringByAppendingPathComponent:file];
        NSString *longPath = [self.documentsDirectory stringByAppendingPathComponent:path];
       // if([self.fileMgr fileExistsAtPath:longPath isDirectory:&isDir] && !isDir){
            [archiver addFileToZip:longPath newname:path];		
        //}
    }
    
    BOOL successCompressing = [archiver CloseZipFile2]; 
    NSLog(@"%@", successCompressing?@"YES":@"NO"); //someBool ? @"YES" : @"NO"
    return successCompressing;
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
