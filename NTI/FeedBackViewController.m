//
//  FeedBackViewController.m
//  NTI
//
//  Created by Mike on 27.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "FeedBackViewController.h"

@implementation FeedBackViewController

@synthesize textView;

-(void)viewDidLoad
{
	[super viewDidLoad]; 
    [textField becomeFirstResponder];
    
    
    UIPickerView *picker = [[UIPickerView alloc] 
                            initWithFrame:CGRectZero];
    picker.delegate = self;
    picker.dataSource = self;
    [picker setShowsSelectionIndicator:YES];
    textField.inputView = picker;
    
    NSArray *ThemesOptionsUnsorted = [NSArray arrayWithObjects:@"Germany", @"Austria", @"Swiss", @"Luxembourg", 
                                  @"Spain", @"Netherlands", @"USA", @"Canada", @"Denmark", @"Great Britain",
                                  @"Finland", @"France", @"Greece", @"Ireland", @"Italy", @"Norway", @"Portugal",
                                  @"Poland", @"Slovenia", @"Sweden", nil];
    ThemesOptions = [ThemesOptionsUnsorted sortedArrayUsingSelector:@selector(localizedCaseInsensitiveCompare:)];
}
// called after the view controller's view is released and set to nil.
// For example, a memory warning which causes the view to be purged. Not invoked as a result of -dealloc.
// So release any properties that are loaded in viewDidLoad or can be recreated lazily.

-(void)viewDidUnload
{
	[super viewDidUnload];
    
//	self.textView = nil;
}

-(void)viewWillAppear:(BOOL)animated 
{
	[super viewWillAppear:animated];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWillShow:) name:UIKeyboardWillShowNotification object:nil];
//    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWillHide:) name:UIKeyboardWillHideNotification object:nil];
}

-(void)viewDidDisappear:(BOOL)animated 
{
    [super viewDidDisappear:animated];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillShowNotification object:nil];
//    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillHideNotification object:nil];
}

-(void)keyboardWillShow:(NSNotification *)aNotification 
{
    
//	// provide my own Save button to dismiss the keyboard
	UIBarButtonItem* previewItem = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemDone
																			  target:self action:@selector(doneAction)];
    //	self.navigationItem.rightBarButtonItem = saveItem;
    navItem.leftBarButtonItem = previewItem;
    
}

- (IBAction)rightItem:(id)sender{
    //вставить сюда код отправки текста стефу
    textView.text = @"";
    textField.text = @"";
    [self doneAction];
    
    //сделать alert сообщение отправлено
}
#pragma mark -
#pragma mark UIPickerViewDataSource

- (NSInteger)numberOfComponentsInPickerView:(UIPickerView *)pickerView
{
    return 1;
}
- (NSInteger)pickerView:(UIPickerView *)pickerView numberOfRowsInComponent:(NSInteger)component
{
    return [ThemesOptions count];
}

#pragma mark -
#pragma mark UIPickerViewDelegate
- (NSString *)pickerView:(UIPickerView *)pickerView titleForRow:(NSInteger)row forComponent:(NSInteger)component
{
    return [ThemesOptions objectAtIndex:row];
}

- (void) pickerView:(UIPickerView *)pickerView didSelectRow:(NSInteger)row inComponent:(NSInteger)component
{
    textField.text = (NSString *)[ThemesOptions objectAtIndex:row];
}

#pragma mark -
#pragma mark UITextViewDelegate

- (void)doneAction
{
	// finish typing text/dismiss the keyboard by removing it as the first responder
    [textView resignFirstResponder];
    [textField resignFirstResponder];
    navItem.leftBarButtonItem = nil;	// this will remove the "save" button
}

@end