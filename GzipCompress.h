//
//  GzipCompress.h
//  NTI
//
//  Created by Елена on 02.05.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <zlib.h>

@interface GzipCompress : NSObject{
    BOOL streamReady; 
    z_stream zStream;
}


+ (NSData *)compressData:(NSData*)uncompressedData error:(NSError **)err;
+ (id)compressor;
- (NSError *)setupStream;
- (NSData *)compressBytes:(Bytef *)bytes length:(NSUInteger)length error:(NSError **)err shouldFinish:(BOOL)shouldFinish;
+ (NSError *)deflateErrorWithCode:(int)code;

@end
