//
//  FeedBackViewController.h
//  NTI
//
//  Created by Mike on 27.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "Crittercism.h"
#import <UIKit/UIKit.h>
#import "ServerCommunication.h"
#import "SBJsonParser.h"

@interface FeedBackViewController : UIViewController <UITextViewDelegate, UITextFieldDelegate, UIPickerViewDelegate, UIPickerViewDataSource> 
{
	IBOutlet UITextView *textView;
    IBOutlet UINavigationItem *navItem; 
    IBOutlet UITextField *textField;
    IBOutlet UIBarButtonItem *rightItem;
    
    NSArray *ThemesOptions;
    
    ServerCommunication *serverCommunication;
    
    IBOutlet UIActivityIndicatorView *waintingIndicator;
    IBOutlet UIView *grayView;
}
@property (nonatomic, retain) UITextView *textView;

- (IBAction)rightItem:(id)sender;
-(IBAction) done:(id) sender;
-(void) feedBackWaitingState;

- (void)doneAction;
@end


