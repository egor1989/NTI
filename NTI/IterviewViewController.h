//
//  IterviewViewController.h
//  NTI
//
//  Created by Елена on 24.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "ServerCommunication.h"

@interface IterviewViewController : UIViewController <UITextFieldDelegate, UIPickerViewDelegate, UIPickerViewDataSource> 
{
    IBOutlet UIScrollView *scrollView;
    
    UIPickerView *insuranceCompanyPicker;
    UIPickerView *sexPicker;
    UIPickerView *autoCategoryPicker;
    UIPickerView *autoPowerPicker;
    
    UIToolbar *insuranceCompanyToolbar;
    
    IBOutlet UITextField *insuranceCompanyField;
    IBOutlet UITextField *sexField;
    IBOutlet UITextField *ageField;
    IBOutlet UITextField *skillField;
    IBOutlet UITextField *numberOfDtpField;
    IBOutlet UITextField *autoCategoryField;
    IBOutlet UITextField *autoPowerField;
    
    //содержание пикеров
    NSArray *insuranceCompanyPickerOptions;
    NSArray *sexPickerOptions;
    NSArray *autoCategoryPickerOptions;
    NSArray *autoPowerPickerOptions;
    
    ServerCommunication *serverCommunication;
}

- (IBAction)doneAction;

@end
