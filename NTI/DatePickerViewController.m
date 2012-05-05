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
    NSDate *minDate = [today dateByAddingTimeInterval: -432000];
    NSDate *maxDate = [today dateByAddingTimeInterval: -900];
    datePicker.minimumDate = minDate;
    datePicker.maximumDate = maxDate;
}

- (IBAction)doneButton:(id)sender {
    NSDate *myDate = datePicker.date;
//    [self transitionFromViewController:_parentModalViewController
//                      toViewController:_parentViewController
//                              duration:0.4
//                               options:UIViewAnimationOptionTransitionFlipFromLeft
//                            animations:nil
//                            completion:^(BOOL done){
//                                [_parentViewController didMoveToParentViewController:self];
//                                [_parentModalViewController removeFromParentViewController];
//                            }];
    [self dismissModalViewControllerAnimated:YES];
    NSLog(@"!!!!!doneButton");
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
