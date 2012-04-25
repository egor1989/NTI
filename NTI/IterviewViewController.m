//
//  IterviewViewController.m
//  NTI
//
//  Created by Елена on 24.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//


#import "IterviewViewController.h"

const int insuranceCompanyPickerTag = 1;
const int sexPickerTag = 2;
const int autoCategoryPickerTag = 3;
const int autoPowerPickerTag = 4;

@implementation IterviewViewController

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    serverCommunication = [[ServerCommunication alloc]init];
    
    scrollView.contentSize = CGSizeMake(320, 600);
    
    //insuranceCompanyPicker
    insuranceCompanyPicker = [[UIPickerView alloc] initWithFrame:CGRectZero];
    insuranceCompanyPicker.tag = insuranceCompanyPickerTag;
    insuranceCompanyPicker.delegate = self;
    insuranceCompanyPicker.dataSource = self;
    [insuranceCompanyPicker setShowsSelectionIndicator:YES];
    insuranceCompanyField.inputView = insuranceCompanyPicker;
    
    
    //sexPicker
    sexPicker = [[UIPickerView alloc] initWithFrame:CGRectZero];
    sexPicker.tag = sexPickerTag;
    sexPicker.delegate = self;
    sexPicker.dataSource = self;
    [sexPicker setShowsSelectionIndicator:YES];
    sexField.inputView = sexPicker;
    
    
    //autoCategoryPicker
    autoCategoryPicker = [[UIPickerView alloc] initWithFrame:CGRectZero];
    autoCategoryPicker.tag = autoCategoryPickerTag;
    autoCategoryPicker.delegate = self;
    autoCategoryPicker.dataSource = self;
    [autoCategoryPicker setShowsSelectionIndicator:YES];
    autoCategoryField.inputView = autoCategoryPicker;
    
    
    //autoPowerPicker
    autoPowerPicker = [[UIPickerView alloc] initWithFrame:CGRectZero];
    autoPowerPicker.tag = autoPowerPickerTag;
    autoPowerPicker.delegate = self;
    autoPowerPicker.dataSource = self;
    [autoPowerPicker setShowsSelectionIndicator:YES];
    autoPowerField.inputView = autoPowerPicker;
    
    
    
    //содержание пикеров
    //    ThemesOptions = [ThemesOptionsUnsorted sortedArrayUsingSelector:@selector(localizedCaseInsensitiveCompare:)];
    insuranceCompanyPickerOptions   = [NSArray arrayWithObjects:@"РосГосСтрах", @"Intouch", nil];
    sexPickerOptions                = [NSArray arrayWithObjects:@"Мужской", @"Женский", nil];
    autoCategoryPickerOptions       = [NSArray arrayWithObjects:@"A", @"B", @"C", @"D", @"E", nil];
    autoPowerPickerOptions          = [NSArray arrayWithObjects:@"80-100 л.с.", @"100-120 л.с.", @"120-140 л.с.", nil];
    
    
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(sync)
     name: @"sync"
     object: nil];
}



#pragma mark -
#pragma mark UIPickerViewDelegate

- (NSString *)pickerView:(UIPickerView *)pickerView titleForRow:(NSInteger)row forComponent:(NSInteger)component
{
    if (pickerView.tag == insuranceCompanyPickerTag)
    {
        if ([insuranceCompanyField.text isEqualToString:@""]) 
            insuranceCompanyField.text = [insuranceCompanyPickerOptions objectAtIndex:row];
        return [insuranceCompanyPickerOptions objectAtIndex:row];
    }
    else if (pickerView.tag == sexPickerTag)
    {
        if ([sexField.text isEqualToString:@""]) 
            sexField.text = [sexPickerOptions objectAtIndex:row];
        return [sexPickerOptions objectAtIndex:row];
    }
    else if (pickerView.tag == autoCategoryPickerTag)
    {
        if ([autoCategoryField.text isEqualToString:@""]) 
            autoCategoryField.text = [autoCategoryPickerOptions objectAtIndex:row];
        return [autoCategoryPickerOptions objectAtIndex:row];
    }
    else if (pickerView.tag == autoPowerPickerTag)
    {
        if ([autoPowerField.text isEqualToString:@""]) 
            autoPowerField.text = [autoPowerPickerOptions objectAtIndex:row];
        return [autoPowerPickerOptions objectAtIndex:row];
    }
    
    return @"Unknown title";
}


- (void) pickerView:(UIPickerView *)pickerView didSelectRow:(NSInteger)row inComponent:(NSInteger)component
{
    if (pickerView.tag == insuranceCompanyPickerTag)
    {
        insuranceCompanyField.text = (NSString *)[insuranceCompanyPickerOptions objectAtIndex:row];
    }
    else if (pickerView.tag == sexPickerTag)
    {
        sexField.text = (NSString *)[sexPickerOptions objectAtIndex:row];
    }
    else if (pickerView.tag == autoCategoryPickerTag)
    {
        autoCategoryField.text = (NSString *)[autoCategoryPickerOptions objectAtIndex:row];
    }
    else if (pickerView.tag == autoPowerPickerTag)
    {
        autoPowerField.text = (NSString *)[autoPowerPickerOptions objectAtIndex:row];
    }
}

#pragma mark -
#pragma mark UIPickerViewDataSource

- (NSInteger)numberOfComponentsInPickerView:(UIPickerView *)pickerView
{
    return 1;
}

- (NSInteger)pickerView:(UIPickerView *)pickerView numberOfRowsInComponent:(NSInteger)component
{
    if (pickerView.tag == insuranceCompanyPickerTag)
    {
        return insuranceCompanyPickerOptions.count;
    }
    else if (pickerView.tag == sexPickerTag)
    {
        return sexPickerOptions.count;
    }
    else if (pickerView.tag == autoCategoryPickerTag)
    {
        return autoCategoryPickerOptions.count;
    }
    else if (pickerView.tag == autoPowerPickerTag)
    {
        return autoPowerPickerOptions.count;
    }
    
    return 1;
}

#pragma mark -



- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

- (IBAction) doneAction{
    NSDictionary *interviewDataDictionary = [[NSDictionary alloc] initWithObjectsAndKeys: 
                                                                    insuranceCompanyField.text, @"company", 
                                                                    ageField.text, @"age",
                                                                    sexField.text, @"sex", 
                                                                    skillField.text, @"skill",
                                                                    numberOfDtpField.text, @"dtp", 
                                                                    autoCategoryField.text, @"autotype",
                                                                    autoPowerField.text, @"autopower",         
                                                                    nil]; 
    [serverCommunication sendInterviewToServerWithData:interviewDataDictionary];
}

@end
