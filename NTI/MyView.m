//
//  MyView.m
//  window
//
//  Created by Kitsune on 13/05/09.
//  Copyright 2009 __MyCompanyName__. All rights reserved.
//

#import "MyView.h"


@implementation MyView

- (void) refreshAccLine:(NSNotification*)theNotice{
    x=[((NSNumber*)[theNotice.userInfo objectForKey: @"accX"]) doubleValue];
    y=[((NSNumber*)[theNotice.userInfo objectForKey: @"accY"]) doubleValue];
}

- (void) refreshGPSLine:(NSNotification*)theNotice{
    course = [myAppDelegate lastLoc].course;
    speed = [myAppDelegate lastLoc].speed;
}

- (id)initWithFrame:(CGRect)frame {
    if (self = [super initWithFrame:frame]) {
    }
    return self;
}

-(void) method{
//    if(f){
    self.clearsContextBeforeDrawing=YES;
        [[NSNotificationCenter defaultCenter]
         addObserver: self
         selector: @selector(refreshAccLine:)
         name: @"plotNotification"
         object: nil];
        f=false;
    
        [[NSNotificationCenter defaultCenter]	
         addObserver: self
         selector: @selector(refreshGPSLine:)
         name: @"locateNotification"
         object: nil];
//    }
}

- (void)drawRect:(CGRect)rect {
    [self method ];
	CGContextRef c = UIGraphicsGetCurrentContext();
	CGContextSetLineWidth (c,4);
	CGFloat red[4] = {1.0f, 0.0f, 0.0f, 1.0f};
	CGContextSetStrokeColor(c, red);
	CGContextBeginPath(c);
    CGContextMoveToPoint(c, 170.0f, 215.0f);
	CGContextAddLineToPoint(c, -x*150.0+170.0, -y*150.0+215.0);
    CGContextMoveToPoint(c, 170.0f, 215.0f);
	CGContextAddLineToPoint(c, 170+speed*sin(M_PI - course), 215+speed*cos(M_PI -course));
	CGContextStrokePath(c);
    CGContextFillEllipseInRect(c, CGRectMake(168, 212, 5, 5));
}



@end
