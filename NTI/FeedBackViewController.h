//
//  FeedBackViewController.h
//  NTI
//
//  Created by Mike on 27.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface FeedBackViewController : UIViewController <UITextViewDelegate, UITextFieldDelegate> 
{
	IBOutlet UITextView *textView;
    IBOutlet UINavigationItem *navItem; 
    IBOutlet UITextField *textField;
    IBOutlet UIBarButtonItem *rightItem;
//    IBOutlet UIBarButtonItem *barButton;
}
@property (nonatomic, retain) UITextView *textView;

- (IBAction)rightItem:(id)sender;
- (void)doneAction;
@end


