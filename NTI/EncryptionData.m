//
//  EncryptionData.m
//  GoodRoads
//
//  Created by Елена on 22.10.11.
//  Copyright (c) 2011 __MyCompanyName__. All rights reserved.
//

#import "EncryptionData.h"


@implementation EncryptionData


-(NSString *)encryptionPassword:(NSData *)password{
    CC_SHA256(password.bytes, password.length, hashedPassword);
    //CC_MD5(password.bytes, password.length, hashedPassword);

    //to hex
    NSMutableString *shaString = [NSMutableString stringWithCapacity:CC_SHA256_DIGEST_LENGTH * 2];
    
    for(int i = 0; i < CC_SHA256_DIGEST_LENGTH; i++) 
        [shaString appendFormat:@"%02x",hashedPassword[i]];
    NSLog(@"hex %@", shaString);
    

    return shaString;
}

/*
- (NSString*)stringWithHexBytes: (NSData *) encrypted {
    static const char hexdigits[] = "0123456789ABCDEF";
    const size_t numBytes = [encrypted length];
    const unsigned char* bytes = [encrypted bytes];
    char *strbuf = (char *)malloc(numBytes * 2 + 1);
    char *hex = strbuf;
    NSString *hexBytes = nil;
    
    for (int i = 0; i<numBytes; ++i) { 
        const unsigned char c = *bytes++;
        *hex++ = hexdigits[(c >> 4) & 0xF];
        *hex++ = hexdigits[(c ) & 0xF];
    }
    *hex = 0;
    hexBytes = [NSString stringWithUTF8String:strbuf];
    free(strbuf);
    return hexBytes;
}

//получать стринг
- (NSData *) stringFromHex:(NSString *)str 
{   
    NSMutableData *stringData = [[NSMutableData alloc] init] ;
    unsigned char whole_byte;
    char byte_chars[3] = {'\0','\0','\0'};
    int i;
    for (i=0; i < [str length] / 2; i++) {
        byte_chars[0] = [str characterAtIndex:i*2];
        byte_chars[1] = [str characterAtIndex:i*2+1];
        whole_byte = strtol(byte_chars, NULL, 16);
        [stringData appendBytes:&whole_byte length:1]; 
    }
    
    return stringData;//[[[NSString alloc] initWithData:stringData encoding:NSASCIIStringEncoding] autorelease];
}


- (NSString *) decryptMessage: (NSString *)message{
    
    NSLog(@"message %@", message);
    NSData * toDecrypt = [self stringFromHex:message];
    
    
    //AES
   // NSData *toDencrypt = [message dataUsingEncoding:NSASCIIStringEncoding];
    
    //must be random string, which made in encryption function
    NSLog(@"random = %@", psString);
    key = [psString dataUsingEncoding:NSASCIIStringEncoding];
//    key = [@"fzxc0WG0lVAj6ATj" dataUsingEncoding:NSASCIIStringEncoding];

    
    CCCryptorStatus status = kCCSuccess;
    NSData *decrypted = [toDecrypt decryptedDataUsingAlgorithm:kCCAlgorithmAES128 key:key options:kCCOptionECBMode error:&status];
    
    NSLog(@"size decrypted = %d", [decrypted length]);
    NSString *decryptStr = [[NSString alloc] initWithData:decrypted encoding:NSASCIIStringEncoding]; 
   // NSString *dencryptHex = [self stringWithHexBytes:dencrypted];
    NSLog(@"text = %@, size = %d", decryptStr, [decryptStr length]);
    return decryptStr;
    
}

*/





@end
