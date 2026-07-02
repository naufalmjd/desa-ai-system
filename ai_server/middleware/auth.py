"""
AuthMiddleware — API Key Authentication untuk FastAPI.
"""

from fastapi import Request, HTTPException, status
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials

from config import get_settings


class ApiKeyMiddleware:
    """Middleware validasi API Key dari header Authorization."""

    def __init__(self):
        self.settings = get_settings()
        self.scheme   = HTTPBearer(auto_error=False)

    async def __call__(self, request: Request, call_next):
        # Lewati health check endpoint
        if request.url.path in ["/", "/health", "/docs", "/openapi.json"]:
            return await call_next(request)

        api_key = self._extract_key(request)

        if not api_key or api_key != self.settings.API_KEY:
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="API Key tidak valid atau tidak ada.",
                headers={"WWW-Authenticate": "Bearer"},
            )

        return await call_next(request)

    def _extract_key(self, request: Request) -> str | None:
        # Authorization: Bearer <key>
        auth = request.headers.get("Authorization", "")
        if auth.startswith("Bearer "):
            return auth[7:]
        # Fallback: header X-API-Key
        return request.headers.get("X-API-Key")


def verify_api_key(request: Request) -> bool:
    """Dependency untuk route-level verification."""
    settings = get_settings()
    api_key  = request.headers.get("Authorization", "").replace("Bearer ", "")
    if api_key != settings.API_KEY:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Unauthorized: API Key tidak valid."
        )
    return True
