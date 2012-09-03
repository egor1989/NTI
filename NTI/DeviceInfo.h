//
//  DeviceInfo.h
//  NTI
//
//  Created by Елена on 28.08.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>
#include "CoreTelephony.h"

@interface DeviceInfo : NSObject


- (NSString *)CTGetIMEI;

@end
