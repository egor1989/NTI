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
    
    serverCommunication = [[ServerCommunication alloc]init];
    UIPickerView *picker = [[UIPickerView alloc] 
                            initWithFrame:CGRectZero];
    picker.delegate = self;
    picker.dataSource = self;
    [picker setShowsSelectionIndicator:YES];
    textField.inputView = picker;
    
    NSArray *ThemesOptionsUnsorted = [NSArray arrayWithObjects:@"Идея", @"Проблема", nil];
    ThemesOptions = [ThemesOptionsUnsorted sortedArrayUsingSelector:@selector(localizedCaseInsensitiveCompare:)];
    
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(feedBackSendAction:)
     name: @"feedBackSend"
     object:nil]; 
}

-(void) feedBackWaitingState{
    waintingIndicator.hidden = NO;
    [waintingIndicator startAnimating];
    grayView.hidden = NO;
}

-(void) feedBackSendAction: (NSNotification*) TheNotice{
    SBJsonParser *jsonParser = [SBJsonParser new];
    NSArray *answerArray = [[NSArray alloc] init];
    int errorCode;
    
    answerArray = [jsonParser objectWithString:[TheNotice object] error:NULL];
    errorCode = [[[answerArray valueForKey:@"error"] valueForKey:@"code"] intValue];
    
    [waintingIndicator stopAnimating];
    grayView.hidden = YES;
    
    if (errorCode == 0) {
        UIAlertView* alertView = [[UIAlertView alloc] initWithTitle:@"Сообщение отправлено" message:@"Ваш отзыв отправлен. Спасибо!" delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
        textView.text = @"";
        textField.text = @"";
        [alertView show];
    }
    else if (errorCode == 33){
        UIAlertView* alertView = [[UIAlertView alloc] initWithTitle:@"Ошибка авторизации" message:@"Пожалуйста перезайдите под своим логином. Это можно сделать в окне статистики." delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
        [alertView show];
    }
    else if (errorCode == 51){
        UIAlertView* alertView = [[UIAlertView alloc] initWithTitle:@"Слишком короткое сообщение" message:@"Пожалуйста, оставьте более развёрнутый отзыв!" delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
        [alertView show];
    }
}

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
    if ([ServerCommunication checkInternetConnection: YES]){
        [serverCommunication sendFeedBackToServerWithTitle:textField.text andBody:textView.text];
        [self feedBackWaitingState];
    }
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