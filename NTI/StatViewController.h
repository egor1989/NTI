//
//  StatViewController.h
//  NTI
//
//  Created by Mike on 07.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "AppDelegate.h"
#import "AuthViewController.h"
#import "RecordAction.h"
#import "ServerCommunication.h"
#import "StatHelpViewController.h"
#import <MessageUI/MessageUI.h>
#import <MessageUI/MFMailComposeViewController.h>
#import "FileController.h"
#import <CoreMotion/CoreMotion.h>

#define myAppDelegate (AppDelegate*) [[UIApplication sharedApplication] delegate]

@interface StatViewController : UITableViewController <UINavigationControllerDelegate, MFMailComposeViewControllerDelegate>{
    UILabel *speedLabel;
    IBOutlet UIButton *loginButton;
    UILabel *qualityDriving;
    UILabel *speedMode;
    UILabel *acceleration;
    UILabel *deceleration;
    UILabel *rotation;
    UILabel *recordLabel;
    UILabel *countKm;
    UILabel *lastTrip;
    IBOutlet UIButton *sendButton;
    ServerCommunication *serverCommunication;
    UIImageView *recordImage;
    IBOutlet UIButton *helpButton;
    NSDictionary *tables;
    IBOutlet UITableView *statTableView;
    FileController *fileController;
}

@property (nonatomic) BOOL *writeAction;
@property (nonatomic, retain) NSDictionary *tables;
@property (nonatomic) UITableView *statTableView;

- (void) speedUpdate;
- (IBAction)loginButton:(id)sender;
//- (IBAction)sendButton:(id)sender;
- (IBAction)helpButton:(id)sender;
- (void) pickOne:(id)sender;
- (void)changeImage;
- (void)parse:(NSString *)result method:(NSString *)method;
- (IBAction) internetUploadSwitch:(id)sender;
- (NSArray *)curentEntries:(NSInteger)index;
@end
