import { Box, Link, Stack, Typography } from '@mui/material';
import React from 'react';

const Header: React.FC = () => {
    return (
        <Box sx={{ display: 'flex', alignItems: 'center', mb: 3 }}>
            <Link href="/" style={{ textDecoration: 'none', color: 'inherit' }}>
                <Stack direction="row" spacing={3} alignItems="center">
                    <Box component="img" src="/logo.png" alt="Logo" width={40} height={40} />
                    <Box>
                        <Typography variant="h4" component="h1" sx={{ fontWeight: 'bold' }}>
                            SMS X Numbers
                        </Typography>
                        <Typography variant="subtitle1" color="text.secondary">
                            Easy Number parsing for import into SMS X
                        </Typography>
                    </Box>
                </Stack>
            </Link>
        </Box>
    );
};

export default Header;
