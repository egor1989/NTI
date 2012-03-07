//
//  StatViewController.h
//  NTI
//
//  Created by Mike on 07.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "AppDelegate.h"

#define myAppDelegate (AppDelegate*) [[UIApplication sharedApplication] delegate]

@interface StatViewController : UITableViewController{
    UILabel *speedLabel;
}

- (void) speedUpdate;

@end
