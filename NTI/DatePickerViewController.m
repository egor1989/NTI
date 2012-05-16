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
    NSDate *minDate = [today dateByAddingTimeInterval: -86400*20];
    NSDate *maxDate = [today dateByAddingTimeInterval: -900];
    datePicker.minimumDate = minDate;
    datePicker.maximumDate = maxDate;
}

- (IBAction)doneButton:(id)sender {
    NSDate *myDate = datePicker.date;
    NSLog(@"%@",myDate);
    NSNumber *date = [NSNumber numberWithDouble:(floor([myDate timeIntervalSince1970] / 86400)*86400)]; //86400 - кол-во секунд в дне
    [self dismissModalViewControllerAnimated:YES];
    [[NSNotificationCenter defaultCenter]	postNotificationName:	@"routePointsRequestSend" object:  date];
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
