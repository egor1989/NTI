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
    
}

+ (NSData *)compressData:(NSData*)uncompressedData error:(NSError **)err;

@end
