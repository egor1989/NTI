//
//  DatePickerViewController.h
//  NTI
//
//  Created by Mike on 11.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface DatePickerViewController : UIViewController{
    IBOutlet UIDatePicker *datePicker;
}

- (IBAction)doneButton:(id)sender;

@end
