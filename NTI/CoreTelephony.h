//
//  CoreTelephony.h
//  NTI
//
//  Created by Елена on 27.08.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//



struct CTServerConnection
{
    int a;
    int b;
    CFMachPortRef myport;
    int c;
    int d;
    int e;
    int f;
    int g;
    int h;
    int i;
};

struct CTResult
{
    int flag;
    int a;
};

struct CTServerConnection * _CTServerConnectionCreate(CFAllocatorRef, void *, int *); 

int *  _CTServerConnectionCopyMobileIdentity(struct CTResult *,   struct CTServerConnection *,  CFStringRef *);

int *  _CTServerConnectionCopyMobileEquipmentInfo(
                                                  struct CTResult * Status,
                                                  struct CTServerConnection * Connection,
                                                  CFMutableDictionaryRef *Dictionary
                                                  );

struct CTServerConnection *sc=NULL;
struct CTResult result;

typedef struct CTServerConnection CTServerConnection;
typedef CTServerConnection* CTServerConnectionRef;



