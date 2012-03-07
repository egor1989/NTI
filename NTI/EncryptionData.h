//
//  EncryptionData.h
//  GoodRoads
//
//  Created by Елена on 22.10.11.
//  Copyright (c) 2011 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CommonCrypto/CommonDigest.h>
#import <CommonCrypto/CommonCryptor.h>
#import "NSData+CommonCrypto.h"


@interface EncryptionData : NSObject{
    unsigned char hashedPassword[16];
    NSData *key;
    NSString *psString;
    NSInteger a;
        
}

-(NSString *)encryptionPassword:(NSData *) password;
- (NSData *) stringFromHex:(NSString *)str ;
- (NSString *) stringWithHexBytes: (NSData *) encrypted;
- (NSString *) decryptMessage: (NSString *) message;


@end
