//
//  FileController.m
//  GoodRoads
//
//  Created by Елена on 18.08.11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#import "FileController.h"



@implementation FileController

@synthesize fileMgr, paths, documentsDirectory, filePath, size, fileCount;

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

+ (void)write:(NSString *)data{
    NSFileManager *fileManager = [NSFileManager defaultManager];
	// get array of paths
	NSArray *dirPath = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
	// get documents directory path
	NSString *docDirectory = [dirPath objectAtIndex:0];
    
    NSString *path = [docDirectory stringByAppendingPathComponent: @"logfile"];
    
    NSLog(@"************* writeToFile *************");
    if(![fileManager fileExistsAtPath:path]){
        [fileManager createFileAtPath:path contents:nil attributes:nil];
        NSLog(@"file created");
    }
    
    NSFileHandle *fileHandle = [NSFileHandle fileHandleForWritingAtPath:path];
    [fileHandle seekToEndOfFile];
    NSData* myData = [data dataUsingEncoding:NSUTF8StringEncoding];
    [fileHandle writeData:myData];
    [fileHandle closeFile];
    NSLog(@"ok");
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
    [fileHandle closeFile];
    NSLog(@"записалось");
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
       
        NSString *deleteFilePath = [self.documentsDirectory stringByAppendingPathComponent:@"logfile"];
        NSLog(@"File : %@", deleteFilePath);
        
        BOOL fileDeleted = [self.fileMgr removeItemAtPath:deleteFilePath error:&error];
        
        if (fileDeleted != YES || error != nil)
        {
            NSLog(@"ERROR!");
        }
    
    return YES;
    
}

- (NSMutableArray *) getAllFiles{
    NSError *error = nil;
    NSMutableArray *allFiles = [[NSMutableArray alloc] init];
    for (NSString *file in [self.fileMgr contentsOfDirectoryAtPath:self.documentsDirectory error:&error])
    {    
        NSString *getFile = [self.documentsDirectory stringByAppendingPathComponent:file];
        NSLog(@"File : %@", getFile);
        [allFiles addObject:getFile];
    }
    return allFiles;
}

- (NSString *) readFile: (NSString *)path{
    
    NSLog(@"************* readFileAction *************");
	//Test whether this file exists before we read it
	if([self.fileMgr fileExistsAtPath:path])
	{
		NSLog(@"File exists we can now try to read it");
        NSString *content = [[NSString alloc] initWithContentsOfFile:path usedEncoding:nil error:nil];
        return content;
	}
	else 
	{
		NSLog(@"File does not exist no need trying to read it");
        return NULL;
	}
}



- (void) countFiles{
    NSError *error = nil;
    size = 0;
    
    fileCount = [[fileMgr contentsOfDirectoryAtPath:documentsDirectory error:&error] count];
   // NSDictionary *fileAttributes = [self.fileMgr fileAttributesAtPath:filePath];
    
    for (NSString *file in [self.fileMgr contentsOfDirectoryAtPath:self.documentsDirectory error:&error])
    {    
        NSString *fileForCount = [self.documentsDirectory stringByAppendingPathComponent:file];
    
        NSDictionary *fileAttributes = [fileMgr attributesOfItemAtPath:fileForCount error:&error];
        if(fileAttributes != nil)
        {
            fileSize = [fileAttributes objectForKey:@"NSFileSize"];
            size += [fileSize integerValue];
            
            NSLog(@"File size: %@ b", fileSize);
            NSLog(@"all size %i", size);
        }
    }
    
}

- (NSArray *) arrayFiles{
    NSError *error = nil;
    NSArray *arrayFiles = [self.fileMgr contentsOfDirectoryAtPath:self.documentsDirectory error:&error];
    return arrayFiles;
                           
}

-(NSData *)makeArchive {
	
   // NSError *error = nil;
   // NSArray *subpaths = [self.fileMgr contentsOfDirectoryAtPath:self.documentsDirectory error:&error];
    
    NSString *archivePath = [documentsDirectory stringByAppendingPathComponent: @"logfile.zip"];
    
    ZipArchive *archiver = [[ZipArchive alloc] init];
    [archiver CreateZipFile2:archivePath];
    //for(NSString *path in subpaths){		
        // Only add it if it's not a directory. ZipArchive will take care of those.
        //NSString *deleteFilePath = [self.documentsDirectory stringByAppendingPathComponent:file];
        NSString *longPath = [self.documentsDirectory stringByAppendingPathComponent:@"logfile"];
       // if([self.fileMgr fileExistsAtPath:longPath isDirectory:&isDir] && !isDir){
            [archiver addFileToZip:longPath newname:@"logfile"];		
        //}
    //}
    
   // NSString *fileContent = [[[NSBundle mainBundle] resourcePath] stringByAppendingPathComponent:@"data.zip"];
    BOOL successCompressing = [archiver CloseZipFile2]; 
    NSLog(@"%@", successCompressing?@"YES":@"NO");
    
    NSData *zipData = [NSData dataWithContentsOfFile:archivePath];
   
    return zipData;
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
