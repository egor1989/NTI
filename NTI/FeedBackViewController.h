//
//  FeedBackViewController.h
//  NTI
//
//  Created by Mike on 27.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "Crittercism.h"
#import <UIKit/UIKit.h>

@interface FeedBackViewController : UIViewController <UITextViewDelegate, UITextFieldDelegate, UIPickerViewDelegate, UIPickerViewDataSource> 
{
	IBOutlet UITextView *textView;
    IBOutlet UINavigationItem *navItem; 
    IBOutlet UITextField *textField;
    IBOutlet UIBarButtonItem *rightItem;
    
    NSArray *ThemesOptions;
}
@property (nonatomic, retain) UITextView *textView;

- (IBAction)rightItem:(id)sender;
-(IBAction) crittercismPressed:(id) sender;
-(IBAction) crashPressed:(id) sender;

- (void)doneAction;
@end


