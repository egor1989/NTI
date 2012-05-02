//
//  GzipCompress.m
//  NTI
//
//  Created by Елена on 02.05.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "GzipCompress.h"

@implementation GzipCompress

+ (NSData *)compressData:(NSData*)uncompressedData error:(NSError **)err{
    NSError *theError = nil;
	//NSData *outputData = [[GzipCompress compressor] compressBytes:(Bytef *)[uncompressedData bytes] length:[uncompressedData length] error:&theError shouldFinish:YES];
	if (theError) {
		if (err) {
			*err = theError;
		}
		return nil;
	}
	//return outputData;
}

@end
