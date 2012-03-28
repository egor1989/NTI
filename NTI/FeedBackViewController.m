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
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(keyboardWillHide:) name:UIKeyboardWillHideNotification object:nil];
}

-(void)viewDidDisappear:(BOOL)animated 
{
    [super viewDidDisappear:animated];
    //[[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillShowNotification object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillHideNotification object:nil];
}

-(void)keyboardWillShow:(NSNotification *)aNotification 
{
	CGRect keyboardRect = [[[aNotification userInfo] objectForKey:UIKeyboardBoundsUserInfoKey] CGRectValue];
    NSTimeInterval animationDuration = [[[aNotification userInfo] objectForKey:UIKeyboardAnimationDurationUserInfoKey] doubleValue];
    CGRect frame = self.view.frame;
    frame.size.height -= keyboardRect.size.height;
    [UIView beginAnimations:@"ResizeForKeyboard" context:nil];
    [UIView setAnimationDuration:animationDuration];
    self.view.frame = frame;
    [UIView commitAnimations];
    
//	// provide my own Save button to dismiss the keyboard
	UIBarButtonItem* previewItem = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemDone
																			  target:self action:@selector(doneAction)];
    //	self.navigationItem.rightBarButtonItem = saveItem;
    navItem.leftBarButtonItem = previewItem;
    
}
 

-(void)keyboardWillHide:(NSNotification *)aNotification
{
   	CGRect keyboardRect = [[[aNotification userInfo] objectForKey:UIKeyboardBoundsUserInfoKey] CGRectValue];
    NSTimeInterval animationDuration = [[[aNotification userInfo] objectForKey:UIKeyboardAnimationDurationUserInfoKey] doubleValue];
    CGRect frame = self.view.frame;
    frame.size.height += keyboardRect.size.height;
    [UIView beginAnimations:@"ResizeForKeyboard" context:nil];
    [UIView setAnimationDuration:animationDuration];
    self.view.frame = frame;
    [UIView commitAnimations];
}

- (IBAction)rightItem:(id)sender{
    //вставить сюда код отправки текста стефу
    textView.text = @"";
    textField.text = @"";
    [self doneAction];
    
    //alert сообщение отправлено
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