const chain = new MiddlewareChain();

// Register additional middleware layers with appropriate order
chain.registerMiddleware('rateLimit', new RateLimitMiddleware(), 15);  // After authentication, before authorization
chain.registerMiddleware('cache', new CacheMiddleware(), 5);           // Before authentication
chain.registerMiddleware('logging', new LoggingMiddleware(), 1);       // First in chain
chain.registerMiddleware('transform', new TransformMiddleware(), 25);  // After authorization, before request validation

// Process request through enhanced middleware chain
const result = await chain.process(request); 