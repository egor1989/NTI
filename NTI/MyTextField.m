//
//  MyTextField.m
//  NTI
//
//  Created by Mike on 03.04.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "MyTextField.h"

@implementation MyTextField

-(BOOL)canPerformAction:(SEL)action withSender:(id)sender 
{
    if ( [UIMenuController sharedMenuController] )
    {
        [UIMenuController sharedMenuController].menuVisible = NO;
    }
    return NO;
}

@end
