import React from 'react';
import { Box, Container, Link, Typography } from '@mui/material';

const Footer: React.FC = () => {
  const currentYear = new Date().getFullYear();

  return (
    <Box component="footer" sx={{ py: 3, mt: 'auto', backgroundColor: 'background.paper' }}>
      <Container maxWidth="lg">
        <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
          <Box>
            <Typography variant="body2" color="text.secondary">
              © {currentYear} SMS X Numbers. All rights reserved.
            </Typography>
          </Box>
          <Box sx={{ display: 'flex', gap: 3 }}>
            <Link href="/how-it-works" color="text.secondary" underline="hover">
              How It Works
            </Link>
            <Link href="/privacy" color="text.secondary" underline="hover">
              Privacy Policy
            </Link>
            <Link href="/developers" color="text.secondary" underline="hover">
              Developers
            </Link>
          </Box>
        </Box>
      </Container>
    </Box>
  );
};

export default Footer;
