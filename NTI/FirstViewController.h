//
//  FirstViewController.h
//  NTI
//
//  Created by Елена on 05.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>

#import "AppDelegate.h"
#define myAppDelegate (AppDelegate*) [[UIApplication sharedApplication] delegate]

#import <CoreMotion/CoreMotion.h>
#import <MessageUI/MessageUI.h>
#import <MessageUI/MFMailComposeViewController.h>
#import "DatabaseActions.h"
#import "FileController.h"
#import "toJSON.h"
#import "ServerCommunication.h"
#import "CSVConverter.h"





@interface FirstViewController : UIViewController <UINavigationControllerDelegate, MFMailComposeViewControllerDelegate>{
    
    IBOutlet UILabel *accX;
    IBOutlet UILabel *accY;
    IBOutlet UILabel *accZ;
    
    IBOutlet UILabel *time;
    IBOutlet UILabel *course;
    IBOutlet UILabel *speed;
    IBOutlet UIImageView *rowCourse;
    IBOutlet UILabel *rowDegrees;

    IBOutlet UILabel *northValue;
    IBOutlet UIImageView *northRow;
    IBOutlet UIImageView *gpsRow;
    
    CMAcceleration userAcceleration;
    CMAcceleration gravity;
    int maxGravityAxe;
    double x,y;
    
    DatabaseActions *databaseAction;
    FileController *fileController;
    
    IBOutlet UIButton *action;
    NSInteger otherFile;
    NSUserDefaults *userDefaults;
    
    IBOutlet UIButton *accelButton;
    BOOL writeToAccelFile;
    NSInteger accelFileNumber;
    IBOutlet UIButton *decelButton;
    BOOL writeToDecelFile;
    NSInteger decelFileNumber;
    IBOutlet UIButton *rightButton;
    BOOL writeToRightRotFile;
    NSInteger rightRotFileNumber;
    IBOutlet UIButton *leftButton;
    BOOL writeToLeftRotFile;
    NSInteger leftRotFileNumber;
    
    BOOL writeToFile;
    NSString *fileName;
    IBOutlet UIButton *mapButton;
    
    BOOL writeInDB;
    NSInteger logFile;
    NSString *fileNameCSV;
    
    
    NSArray *keys;
    NSMutableArray *forJSON;
   // NSDictionary *entries;
    
    toJSON *jsonConvert;
    
    CLLocation *location;
    int k;
    
    CSVConverter *csvConverter;
    BOOL writeToLog;
    NSString *type;
    
    
}

- (IBAction)acceleration:(id)sender;
- (IBAction)deceleration:(id)sender;
- (IBAction)rightRot:(id)sender;
- (IBAction)leftRot:(id)sender;
- (IBAction)actionButton:(id)sender;
- (IBAction)clearDB:(id)sender;
- (IBAction)sendFile:(id)sender;
- (IBAction)mapLogFile:(id)sender;



@property (nonatomic, retain) NSString *fileName;
@property (nonatomic, retain) NSString *type;


- (void) showGPS;
- (void)sendFile;
- (void)infoAboutFiles;
- (NSString *)convertSize: (NSInteger)size;
- (void)sendToServer;
@end
