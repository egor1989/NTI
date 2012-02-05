//
//  FirstViewController.h
//  NTI
//
//  Created by Елена on 05.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
<<<<<<< HEAD
#import "AppDelegate.h"
#define myAppDelegate (AppDelegate*) [[UIApplication sharedApplication] delegate]
=======
#import <CoreMotion/CoreMotion.h>
>>>>>>> 217e378b44d0790197998a43174f8903f9c8d311

@interface FirstViewController : UIViewController{
    
    IBOutlet UILabel *accX;
    IBOutlet UILabel *accY;
    IBOutlet UILabel *accZ;
    
    IBOutlet UILabel *time;
    IBOutlet UILabel *course;
    IBOutlet UILabel *longitude;
    IBOutlet UILabel *speed;
<<<<<<< HEAD
    IBOutlet UILabel *latitude;
=======
    IBOutlet UILabel *altitude;
    
    CMAcceleration currentAcceleration;
>>>>>>> 217e378b44d0790197998a43174f8903f9c8d311
}

- (IBAction)acceleration:(id)sender;
- (IBAction)deceleration:(id)sender;
- (IBAction)rotation:(id)sender;
- (IBAction)action:(id)sender;

- (void) showGPS;

@end
