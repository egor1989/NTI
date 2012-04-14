//
//  DatePickerViewController.m
//  NTI
//
//  Created by Mike on 11.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "DatePickerViewController.h"

@implementation DatePickerViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    NSDate *today = [NSDate date];
    NSDate *twoDayBefore = [today dateByAddingTimeInterval: -432000];
    NSDate *maxDate = [today dateByAddingTimeInterval: -900];
    datePicker.minimumDate = twoDayBefore;
    datePicker.maximumDate = maxDate;
}

- (IBAction)doneButton:(id)sender {
    NSDate *myDate = datePicker.date;
    [self dismissModalViewControllerAnimated:YES];
    [[NSNotificationCenter defaultCenter]	postNotificationName:	@"routePointsRequestSend" object:  myDate];
}

#pragma mark - View lifecycle


- (void)viewDidUnload
{
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

@end
