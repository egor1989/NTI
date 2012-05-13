//
//  GzipCompress.m
//  NTI
//
//  Created by Елена on 02.05.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//
#define DATA_CHUNK_SIZE 262144 // Deal with gzipped data in 256KB chunks 262144
#define COMPRESSION_AMOUNT Z_DEFAULT_COMPRESSION


#import "GzipCompress.h"

@implementation GzipCompress

+ (NSData *)compressData:(NSData*)uncompressedData error:(NSError **)err{
    NSError *theError = nil;
	NSData *outputData = [[GzipCompress compressor] compressBytes:(Bytef *)[uncompressedData bytes] length:[uncompressedData length] error:&theError shouldFinish:YES];
	if (theError) {
		if (err) {
			*err = theError;
		}
		return nil;
	}
	return outputData;
}

+ (id)compressor
{
	GzipCompress *compressor = [[self alloc] init];
	[compressor setupStream];
	return compressor;
}

- (NSError *)setupStream
{
	if (streamReady) {
		return nil;
	}
	// Setup the inflate stream
	zStream.zalloc = Z_NULL;
	zStream.zfree = Z_NULL;
	zStream.opaque = Z_NULL;
	zStream.avail_in = 0;
	zStream.next_in = 0;
	int status = deflateInit2(&zStream, COMPRESSION_AMOUNT, Z_DEFLATED, (15+16), 8, Z_DEFAULT_STRATEGY);
	if (status != Z_OK) {
		return [[self class] deflateErrorWithCode:status];
	}
	streamReady = YES;
	return nil;
}

+ (NSError *)deflateErrorWithCode:(int)code
{
	return [NSError errorWithDomain:@"goodroads.ru" code:1 userInfo:[NSDictionary dictionaryWithObjectsAndKeys:[NSString stringWithFormat:@"Compression of data failed with code %hi",code],NSLocalizedDescriptionKey,nil]];
}

- (NSData *)compressBytes:(Bytef *)bytes length:(NSUInteger)length error:(NSError **)err shouldFinish:(BOOL)shouldFinish
{
	if (length == 0) return nil;
    
	NSUInteger halfLength = length/2;
    
	// We'll take a guess that the compressed data will fit in half the size of the original (ie the max to compress at once is half DATA_CHUNK_SIZE), if not, we'll increase it below
	NSMutableData *outputData = [[NSMutableData alloc] initWithLength:halfLength]; 

	int status;
    
	zStream.next_in = bytes;
	zStream.avail_in = (unsigned int)length;
	zStream.avail_out = 0;
    
	NSInteger bytesProcessedAlready = zStream.total_out;
	while (zStream.avail_out == 0) {
        
		if (zStream.total_out-bytesProcessedAlready >= [outputData length]) {
			[outputData increaseLengthBy:halfLength];
		}
        
		zStream.next_out = (Bytef*)[outputData mutableBytes] + zStream.total_out-bytesProcessedAlready;
		zStream.avail_out = (unsigned int)([outputData length] - (zStream.total_out-bytesProcessedAlready));
		status = deflate(&zStream, shouldFinish ? Z_FINISH : Z_NO_FLUSH);
        
		if (status == Z_STREAM_END) {
			break;
		} else if (status != Z_OK) {
			if (err) {
				*err = [[self class] deflateErrorWithCode:status];
			}
			//return NO;
		}
	}
    
	// Set real length
	[outputData setLength: zStream.total_out-bytesProcessedAlready];
    NSLog(@"output = %@", outputData);
	return outputData;
}


- (NSData *)gzipDeflate: (NSData*)uncompressedData 
{
	if ([uncompressedData length] == 0) return uncompressedData;
	
	z_stream strm;
	
	strm.zalloc = Z_NULL;
	strm.zfree = Z_NULL;
	strm.opaque = Z_NULL;
	strm.total_out = 0;
	strm.next_in=(Bytef *)[uncompressedData bytes];
	strm.avail_in = [uncompressedData length];
	
	// Compresssion Levels:
	//   Z_NO_COMPRESSION
	//   Z_BEST_SPEED
	//   Z_BEST_COMPRESSION
	//   Z_DEFAULT_COMPRESSION
	
	if (deflateInit2(&strm, Z_DEFAULT_COMPRESSION, Z_DEFLATED, (15+16), 8, Z_DEFAULT_STRATEGY) != Z_OK) return nil;
	
	NSMutableData *compressed = [NSMutableData dataWithLength:16384];  // 16K chunks for expansion
	
	do {
		
		if (strm.total_out >= [compressed length])
			[compressed increaseLengthBy: 16384];
		
		strm.next_out = [compressed mutableBytes] + strm.total_out;
		strm.avail_out = [compressed length] - strm.total_out;
		
		deflate(&strm, Z_FINISH);  
		
	} while (strm.avail_out == 0);
	
	deflateEnd(&strm);
	
	[compressed setLength: strm.total_out];
	return [NSData dataWithData:compressed];
}


- (NSData *)gzipInflate: (NSData*)compressedData
{
	if ([compressedData length] == 0) return compressedData;
	
	unsigned full_length = [compressedData length];
	unsigned half_length = [compressedData length] / 2;
	
	NSMutableData *decompressed = [NSMutableData dataWithLength: full_length + half_length];
	BOOL done = NO;
	int status;
	
	z_stream strm;
	strm.next_in = (Bytef *)[compressedData bytes];
	strm.avail_in = [compressedData length];
	strm.total_out = 0;
	strm.zalloc = Z_NULL;
	strm.zfree = Z_NULL;
	
	if (inflateInit2(&strm, (15+32)) != Z_OK) return nil;
	while (!done)
	{
		// Make sure we have enough room and reset the lengths.
		if (strm.total_out >= [decompressed length])
			[decompressed increaseLengthBy: half_length];
		strm.next_out = [decompressed mutableBytes] + strm.total_out;
		strm.avail_out = [decompressed length] - strm.total_out;
		
		// Inflate another chunk.
		status = inflate (&strm, Z_SYNC_FLUSH);
		if (status == Z_STREAM_END) done = YES;
		else if (status != Z_OK) break;
	}
	if (inflateEnd (&strm) != Z_OK) return nil;
	
	// Set real length.
	if (done)
	{
		[decompressed setLength: strm.total_out];
        NSLog(@"decompressed = %@", [NSData dataWithData: decompressed]);
		return [NSData dataWithData: decompressed];
	}
	else return nil;
}



@end
