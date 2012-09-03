//
//  DeviceInfo.m
//  NTI
//
//  Created by Елена on 28.08.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "DeviceInfo.h"

@implementation DeviceInfo

- (NSString *)CTGetIMEI
{
    struct CTResult it;
    NSMutableDictionary *dict;
    CTServerConnectionRef conn;

    conn = _CTServerConnectionCreate(kCFAllocatorDefault, NULL, NULL);

    _CTServerConnectionCopyMobileEquipmentInfo(&it, conn, (CFMutableDictionaryRef*)CFBridgingRetain(dict));
    CFRelease(conn);
    //return [dict objectForKey: kCTMobileEquipmentInfoIMEI];
    return @"";
}


@end
