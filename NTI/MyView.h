//
//  MyView.h
//  window
//
//  Created by Kitsune on 13/05/09.
//  Copyright 2009 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "math.h"
#import "AppDelegate.h"
#define myAppDelegate (AppDelegate*) [[UIApplication sharedApplication] delegate]

@interface MyView : UIView {
    double x,y,course,speed;
    bool f;
}

@end
